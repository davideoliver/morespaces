<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\User;
use App\Repositories\ContactMessageRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactService
{
    public function __construct(
        private readonly ContactMessageRepository $messages,
    ) {
    }

    /** @param array{nome: string, contatoEmail: string, mensagem: string} $data */
    public function create(array $data, ?User $user = null): ContactMessage
    {
        return $this->messages->create([
            'user_id' => $user?->id,
            'name' => $data['nome'],
            'email' => $data['contatoEmail'],
            'message' => $data['mensagem'],
        ]);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->messages->paginate($perPage);
    }
}
