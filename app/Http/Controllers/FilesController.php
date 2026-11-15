<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Controla a área de arquivos do M.G.E.
 */
class FilesController extends Controller
{
    public function index(): View
    {
        return view('files', [
            'levels' => range(0, config('morespaces.levels_per_page', 9) - 1),
        ]);
    }

    public function show(int $level): Response
    {
        return response()->view('files.not-found', ['level' => $level], 404);
    }

}
