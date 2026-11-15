<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\DangerLevel;
use App\Models\Level;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        $level = $this->route('level');

        return $level instanceof Level && ($this->user()?->can('update', $level) ?? false);
    }

    public function rules(): array
    {
        $level = $this->route('level');

        return [
            'number' => [
                'sometimes',
                'nullable',
                'integer',
                'between:0,9999',
                Rule::unique('levels', 'number')->ignore($level instanceof Level ? $level->id : null),
            ],
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'danger_level' => ['required', Rule::enum(DangerLevel::class)],
        ];
    }

    public function messages(): array
    {
        return (new StoreLevelRequest)->messages();
    }
}
