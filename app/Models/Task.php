<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'parent_task_id', 'title', 'description', 'assigned_to',
        'created_by', 'status', 'priority', 'start_date', 'due_date',
        'progress', 'estimated_hours', 'sort_order', 'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    public array $statusColorMap = [
        'todo' => 'secondary',
        'in_progress' => 'primary',
        'review' => 'warning',
        'completed' => 'success',
    ];

    public array $statusLabelMap = [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'In Review',
        'completed' => 'Completed',
    ];

    public array $priorityColorMap = [
        'low' => 'success',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(TaskStatusLog::class)->latest();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'completed';
    }

    /** Update status/progress and write an audit log entry, keeping the two fields consistent. */
    public function applyStatusChange(string $newStatus, ?int $newProgress, ?int $userId, ?string $note = null): void
    {
        $oldStatus = $this->status;
        $oldProgress = $this->progress;

        $progressByStatus = [
            'todo' => 0,
            'in_progress' => max(10, $newProgress ?? $this->progress),
            'review' => max(80, $newProgress ?? $this->progress),
            'completed' => 100,
        ];

        $this->status = $newStatus;
        $this->progress = $newProgress ?? ($progressByStatus[$newStatus] ?? $this->progress);
        $this->completed_at = $newStatus === 'completed' ? now() : null;
        $this->save();

        $this->statusLogs()->create([
            'user_id' => $userId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_progress' => $oldProgress,
            'new_progress' => $this->progress,
            'note' => $note,
        ]);

        $this->project?->recalculateProgress();
    }
}
