<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\Notification;
use App\Models\User;
use App\Mail\ComplaintCreatedMail;
use App\Mail\ComplaintStatusUpdatedMail;
use App\Mail\ComplaintAssignedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ComplaintService
{
    /**
     * Create a new complaint.
     */
    public function createComplaint(array $data, int $userId): Complaint
    {
        return DB::transaction(function () use ($data, $userId) {
            $user = User::findOrFail($userId);
            
            // Handle file upload if present
            $attachmentPath = null;
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $attachmentPath = $data['attachment']->store('attachments', 'public');
            }

            $complaint = Complaint::create([
                'user_id' => $userId,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'Medium',
                'status' => 'Pending',
                'location' => $data['location'] ?? null,
                'attachment' => $attachmentPath,
            ]);

            // Create timeline update
            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'updated_by' => $userId,
                'old_status' => null,
                'new_status' => 'Pending',
                'comment' => 'Complaint submitted successfully.',
                'update_type' => 'status_change',
                'created_at' => now(),
            ]);

            // Create notification for all admins
            $admins = User::admins()->active()->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'complaint_id' => $complaint->id,
                    'title' => 'New Complaint Filed',
                    'message' => "A new complaint \"{$complaint->title}\" has been submitted.",
                    'type' => 'info',
                    'created_at' => now(),
                ]);
            }

            // Audit Log
            AuditService::log(
                $userId,
                'create_complaint',
                'Complaint',
                $complaint->id,
                null,
                $complaint->toArray()
            );

            // Send email to admins & user
            try {
                if (setting('enable_email_notifications', '1') === '1') {
                    // Send to submitter
                    Mail::to($user->email)->send(new ComplaintCreatedMail($user, $complaint));
                    
                    // Send to admins
                    foreach ($admins as $admin) {
                        Mail::to($admin->email)->send(new ComplaintCreatedMail($admin, $complaint, true));
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to send email for complaint creation: " . $e->getMessage());
            }

            return $complaint;
        });
    }

    /**
     * Update status of a complaint.
     */
    public function updateStatus(int $complaintId, string $status, int $adminId, ?string $comment = null): Complaint
    {
        return DB::transaction(function () use ($complaintId, $status, $adminId, $comment) {
            $complaint = Complaint::findOrFail($complaintId);
            $oldStatus = $complaint->status;
            $oldValues = $complaint->toArray();

            $complaint->status = $status;
            if ($status === 'Resolved') {
                $complaint->resolved_at = now();
            } else {
                $complaint->resolved_at = null;
            }
            $complaint->save();

            // Create timeline update
            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'updated_by' => $adminId,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'comment' => $comment,
                'update_type' => 'status_change',
                'created_at' => now(),
            ]);

            // Notify complaint owner
            $owner = $complaint->user;
            Notification::create([
                'user_id' => $owner->id,
                'complaint_id' => $complaint->id,
                'title' => 'Complaint Status Updated',
                'message' => "Your complaint \"{$complaint->title}\" status is now: {$status}.",
                'type' => $status === 'Resolved' ? 'success' : ($status === 'Rejected' ? 'error' : 'warning'),
                'created_at' => now(),
            ]);

            // Audit Log
            AuditService::log(
                $adminId,
                'update_status',
                'Complaint',
                $complaint->id,
                $oldValues,
                $complaint->toArray()
            );

            // Email Notification
            try {
                if (setting('enable_email_notifications', '1') === '1') {
                    Mail::to($owner->email)->send(new ComplaintStatusUpdatedMail($owner, $complaint, $status, $comment));
                }
            } catch (\Exception $e) {
                Log::error("Failed to send email for status update: " . $e->getMessage());
            }

            return $complaint;
        });
    }

    /**
     * Assign complaint to an admin.
     */
    public function assignComplaint(int $complaintId, int $adminId, int $assignedToId): Complaint
    {
        return DB::transaction(function () use ($complaintId, $adminId, $assignedToId) {
            $complaint = Complaint::findOrFail($complaintId);
            $assignee = User::findOrFail($assignedToId);
            $oldValues = $complaint->toArray();

            $complaint->assigned_to = $assignedToId;
            $complaint->save();

            // Create timeline entry
            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'updated_by' => $adminId,
                'old_status' => null,
                'new_status' => $complaint->status,
                'comment' => "Assigned to {$assignee->name}.",
                'update_type' => 'assignment',
                'created_at' => now(),
            ]);

            // Notify assigned admin
            Notification::create([
                'user_id' => $assignedToId,
                'complaint_id' => $complaint->id,
                'title' => 'New Complaint Assigned',
                'message' => "You have been assigned the complaint: \"{$complaint->title}\".",
                'type' => 'info',
                'created_at' => now(),
            ]);

            // Audit Log
            AuditService::log(
                $adminId,
                'assign_complaint',
                'Complaint',
                $complaint->id,
                $oldValues,
                $complaint->toArray()
            );

            // Email Notification
            try {
                if (setting('enable_email_notifications', '1') === '1') {
                    Mail::to($assignee->email)->send(new ComplaintAssignedMail($assignee, $complaint));
                }
            } catch (\Exception $e) {
                Log::error("Failed to send assignment email: " . $e->getMessage());
            }

            return $complaint;
        });
    }

    /**
     * Change priority of a complaint.
     */
    public function changePriority(int $complaintId, int $adminId, string $newPriority): Complaint
    {
        return DB::transaction(function () use ($complaintId, $adminId, $newPriority) {
            $complaint = Complaint::findOrFail($complaintId);
            $oldValues = $complaint->toArray();
            $oldPriority = $complaint->priority;

            $complaint->priority = $newPriority;
            $complaint->save();

            // Create timeline entry
            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'updated_by' => $adminId,
                'old_status' => null,
                'new_status' => $complaint->status,
                'comment' => "Priority changed from {$oldPriority} to {$newPriority}.",
                'update_type' => 'priority_change',
                'created_at' => now(),
            ]);

            // Audit Log
            AuditService::log(
                $adminId,
                'change_priority',
                'Complaint',
                $complaint->id,
                $oldValues,
                $complaint->toArray()
            );

            return $complaint;
        });
    }

    /**
     * Add comment to complaint timeline.
     */
    public function addComment(int $complaintId, int $adminId, string $comment): ComplaintUpdate
    {
        return DB::transaction(function () use ($complaintId, $adminId, $comment) {
            $complaint = Complaint::findOrFail($complaintId);

            $update = ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'updated_by' => $adminId,
                'old_status' => null,
                'new_status' => $complaint->status,
                'comment' => $comment,
                'update_type' => 'comment',
                'created_at' => now(),
            ]);

            // Notify complaint owner (if commenter is not the owner)
            if ($complaint->user_id !== $adminId) {
                Notification::create([
                    'user_id' => $complaint->user_id,
                    'complaint_id' => $complaint->id,
                    'title' => 'New Comment on Complaint',
                    'message' => "An administrator added a comment to your complaint: \"{$complaint->title}\".",
                    'type' => 'info',
                    'created_at' => now(),
                ]);
            }

            // Audit Log
            AuditService::log(
                $adminId,
                'add_comment',
                'ComplaintUpdate',
                $update->id,
                null,
                $update->toArray()
            );

            return $update;
        });
    }
}
