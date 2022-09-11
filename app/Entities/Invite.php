<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;

class Invite extends Entity
{
    protected $dates = ['created_at', 'expired_at'];
    protected $casts = [
        'job_id' => 'int',
        'token'  => 'string',
    ];

    public function isExpired(): bool
    {
        if ($this->expired_at === null) {
            return false;
        }

        return $this->expired_at->isBefore(Time::now()); // @phpstan-ignore-line
    }
}
