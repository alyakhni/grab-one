<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending' => 'Pending',
        'working_on_it' => 'Working on it',
        'resolved' => 'Resolved',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'is_read',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }
}