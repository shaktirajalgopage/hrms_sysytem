<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAppNotificastion extends Model
{
    use HasFactory;
    protected $table = 'user_app_notificastions';

     protected $fillable = [
        'title',
        'body',
        'type',
        'category',
        'icon',
        'icon_color',
        'target_roles',
        'target_employee_ids',
        'is_broadcast',
        'action_url',
        'action_label',
        'scheduled_at',
        'sent_at',
        'status',
        'created_by',
    ];
   
    protected $casts = [
        'target_roles'        => 'array',
        'target_employee_ids' => 'array',
        'is_broadcast'        => 'boolean',
        'scheduled_at'        => 'datetime',
        'sent_at'             => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(EmployeeNotification::class, 'notification_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'employee_notifications', 'notification_id', 'user_id')
                    ->withPivot(['is_read', 'read_at', 'dismissed_at'])
                    ->withTimestamps();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeDueForSending($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '<=', now());
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function categoryList(): array
    {
        return [
            'ALL'           => 'All',
            'ANNOUNCEMENTS' => 'Announcements',
            'PERSONAL'      => 'Personal',
            'LEAVES'        => 'Leaves',
            'HOLIDAY'       => 'Holiday',
            'SYSTEM'        => 'System',
        ];
    }

    public static function typeList(): array
    {
        return [
            'general'      => 'General',
            'policy'       => 'Policy',
            'salary'       => 'Salary',
            'holiday'      => 'Holiday',
            'leave'        => 'Leave',
            'security'     => 'Security',
            'announcement' => 'Announcement',
            'personal'     => 'Personal',
        ];
    }

    public static function iconColorMap(): array
    {
        return [
            'policy'       => 'secondary',
            'salary'       => 'success',
            'holiday'      => 'warning',
            'leave'        => 'danger',
            'security'     => 'dark',
            'announcement' => 'primary',
            'general'      => 'info',
            'personal'     => 'primary',
        ];
    }

    public function getIconClassAttribute(): string
    {
        return match ($this->type) {
            'policy'       => 'fas fa-file-alt',
            'salary'       => 'fas fa-money-bill-wave',
            'holiday'      => 'fas fa-umbrella-beach',
            'leave'        => 'fas fa-calendar-check',
            'security'     => 'fas fa-shield-alt',
            'announcement' => 'fas fa-bullhorn',
            'personal'     => 'fas fa-user',
            default        => 'fas fa-bell',
        };
    }

    public function getReadCountAttribute(): int
    {
        return $this->recipients()->where('is_read', true)->count();
    }

    public function getTotalRecipientsCountAttribute(): int
    {
        return $this->recipients()->count();
    }
}
