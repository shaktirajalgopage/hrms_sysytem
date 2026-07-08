<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Task;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Relations\HasMany; 


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['role_id','name','email','phone','status','password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // One

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }
    public function employee(): HasOne {
    return $this->hasOne(Employee::class, 'user_id');
}

public function assignedTasks(): HasMany
{
    return $this->hasMany(Task::class, 'assigned_to');
}

// --- Tasks this user created ---
public function createdTasks(): HasMany
{
    return $this->hasMany(Task::class, 'created_by');
}

// --- Tickets raised by this user ---
public function raisedTickets(): HasMany
{
    return $this->hasMany(Ticket::class, 'raised_by');
}

// --- Tickets assigned to this user ---
public function assignedTickets(): HasMany
{
    return $this->hasMany(Ticket::class, 'assigned_to');
}
// public function notifications(): BelongsTo {
//         return $this->belongsTo(Notification::class);
//     }
}
