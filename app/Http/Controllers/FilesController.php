<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Models\Level;
use App\Services\LevelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Controla a área de arquivos do M.G.E.
 */
class FilesController extends Controller
{
    public function __construct(
        private readonly LevelService $levels,
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->query('q');

        return view('files', [
            'levels' => $this->levels->paginate(is_string($search) ? $search : null),
            'search' => is_string($search) ? $search : null,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Level::class);

        return view('files.create', ['level' => new Level]);
    }

    public function store(StoreLevelRequest $request): RedirectResponse
    {
        $level = $this->levels->create($request->validated(), $request->user());

        return redirect()->route('files.show', $level)->with('success', 'Nível criado com sucesso.');
    }

    public function show(Level $level): View
    {
        return view('files.show', compact('level'));
    }

    public function edit(Request $request, Level $level): View
    {
        Gate::authorize('update', $level);

        return view('files.edit', compact('level'));
    }

    public function update(UpdateLevelRequest $request, Level $level): RedirectResponse
    {
        $level = $this->levels->update($level, $request->validated());

        return redirect()->route('files.show', $level)->with('success', 'Nível atualizado com sucesso.');
    }

    public function destroy(Level $level): RedirectResponse
    {
        Gate::authorize('delete', $level);
        $this->levels->delete($level);

        return redirect()->route('files.index')->with('success', 'Nível excluído com sucesso.');
    }
}
