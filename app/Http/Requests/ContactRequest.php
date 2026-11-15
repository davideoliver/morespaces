<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => strip_tags(trim((string) $this->input('nome'))),
            'contatoEmail' => strip_tags(trim((string) $this->input('contatoEmail'))),
            'mensagem' => strip_tags(trim((string) $this->input('mensagem'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:120'],
            'contatoEmail' => ['required', 'email', 'max:190'],
            'mensagem' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe seu nome.',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome deve ter no máximo 120 caracteres.',
            'contatoEmail.required' => 'Informe seu e-mail.',
            'contatoEmail.email' => 'Informe um e-mail válido.',
            'contatoEmail.max' => 'O e-mail deve ter no máximo 190 caracteres.',
            'mensagem.required' => 'Informe sua mensagem.',
            'mensagem.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
            'mensagem.max' => 'A mensagem deve ter no máximo 2000 caracteres.',
        ];
    }
}
