<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'title', 'description', 'client_name', 'department_id',
        'start_date', 'end_date', 'status', 'priority', 'budget',
        'progress', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'progress' => 'integer',
    ];

    /** Map of status => bootstrap-soft color key used across the UI */
    public array $statusColorMap = [
        'planning' => 'info',
        'active' => 'primary',
        'on_hold' => 'warning',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];

    public array $priorityColorMap = [
        'low' => 'success',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /** Recalculate cached progress from completed vs total tasks and persist it. */
    public function recalculateProgress(): int
    {
        $total = $this->tasks()->count();

        $progress = $total === 0
            ? 0
            : (int) round($this->tasks()->avg('progress'));

        $this->forceFill(['progress' => $progress])->save();

        return $progress;
    }

    public function getTaskStatsAttribute(): array
    {
        $tasks = $this->tasks;

        return [
            'total' => $tasks->count(),
            'todo' => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'review' => $tasks->where('status', 'review')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'overdue' => $tasks->where('status', '!=', 'completed')
                ->filter(fn ($t) => $t->due_date && $t->due_date->isPast())
                ->count(),
        ];
    }

    public function isOverdue(): bool
    {
        return $this->end_date
            && $this->end_date->isPast()
            && $this->status !== 'completed';
    }
}
