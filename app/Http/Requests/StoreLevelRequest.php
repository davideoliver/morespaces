<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\DangerLevel;
use App\Models\Level;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Level::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'number' => ['sometimes', 'nullable', 'integer', 'between:0,9999', Rule::unique('levels', 'number')],
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'danger_level' => ['required', Rule::enum(DangerLevel::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'number.integer' => 'O número do nível deve ser um inteiro.',
            'number.between' => 'O número do nível deve estar entre 0 e 9999.',
            'number.unique' => 'Este número de nível já está cadastrado.',
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode exceder 120 caracteres.',
            'summary.max' => 'O resumo não pode exceder 255 caracteres.',
            'description.required' => 'A descrição é obrigatória.',
            'description.max' => 'A descrição não pode exceder 5000 caracteres.',
            'danger_level.required' => 'O nível de perigo é obrigatório.',
            'danger_level.enum' => 'Selecione um nível de perigo válido.',
        ];
    }
}
