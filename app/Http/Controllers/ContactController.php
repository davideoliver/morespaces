<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Controla o formulário de contato demonstrativo.
 */
class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contacts,
    ) {
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $this->contacts->create($request->validated(), $request->user());

        return redirect()->to(route('home').'#contact')
            ->with('success', 'Mensagem enviada com sucesso!');
    }

    public function index(): View
    {
        Gate::authorize('viewAny', ContactMessage::class);

        return view('admin.messages', [
            'messages' => $this->contacts->paginate(),
        ]);
    }
}
