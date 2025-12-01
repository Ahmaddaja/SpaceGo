<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
    'title',
    'message',
    'is_read',
    'icon',
    'user_id',
    'category',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

public function getIconClass()
{
    switch ($this->category) {
        case 'message':
            return 'fas fa-envelope text-primary';
        case 'friend_request':
            return 'fas fa-user-friends text-info';
        case 'report':
            return 'fas fa-exclamation-triangle text-warning';
        case 'transaction':
            return 'fas fa-shopping-cart text-success';
        case 'new_user':
            return 'fas fa-user-plus text-info';
        default:
            return 'fas fa-bell text-secondary';
    }
}

    protected function getDefaultIcon()
    {
        switch ($this->category) {
            case 'message':
                return 'fas fa-envelope';
            case 'friend_request':
                return 'fas fa-user-plus';
            case 'report':
                return 'fas fa-flag';
            default:
                return 'fas fa-bell';
        }
    }
}
