<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DangerLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'summary',
        'description',
        'danger_level',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'danger_level' => DangerLevel::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
