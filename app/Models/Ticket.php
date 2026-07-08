<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'project_id', 'task_id', 'subject', 'description',
        'raised_by', 'assigned_to', 'category', 'priority', 'status', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public array $statusColorMap = [
        'open' => 'danger',
        'in_progress' => 'warning',
        'resolved' => 'info',
        'closed' => 'success',
    ];

    public array $priorityColorMap = [
        'low' => 'success',
        'medium' => 'info',
        'high' => 'warning',
        'critical' => 'danger',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });
    }

    /** Generates a sequential, year-scoped ticket number, e.g. TCK-2026-0001 */
    public static function generateTicketNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "TCK-{$year}-";

        $last = static::where('ticket_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('ticket_number');

        $nextSeq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->oldest();
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'closed'
            && $this->status !== 'resolved'
            && $this->created_at->diffInDays(now()) > 3
            && in_array($this->priority, ['high', 'critical']);
    }
}
