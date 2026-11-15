<?php

declare(strict_types=1);

namespace App\Enums;

enum DangerLevel: string
{
    case Safe = 'safe';
    case Low = 'low';
    case Moderate = 'moderate';
    case High = 'high';
    case Lethal = 'lethal';
}
