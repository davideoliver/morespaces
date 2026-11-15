<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Member = 'member';
    case Agent = 'agent';
    case Admin = 'admin';
}
