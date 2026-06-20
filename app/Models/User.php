<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Legacy table doesn't have updated_at
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Legacy table doesn't have remember_token
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Get the user's avatar URL. Returns uploaded avatar or the default fallback.
     */
    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar);
        }
        return asset('images/default-avatar.jpg');
    }

    /**
     * Get initials (up to 2 chars) for the fallback avatar.
     */
    public function initials(): string
    {
        $parts = explode(' ', trim($this->name));
        $init  = strtoupper(substr($parts[0], 0, 1));
        if (count($parts) > 1) {
            $init .= strtoupper(substr(end($parts), 0, 1));
        }
        return $init;
    }

    /**
     * Get the projects where this user is the lead.
     */
    public function ledProjects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Project::class, 'project_lead_id');
    }

    /**
     * Get the tasks assigned to this user.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get the projects this user is a member of.
     */
    public function memberOfProjects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members', 'user_id', 'project_id');
    }
}
