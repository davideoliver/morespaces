<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Http\Resources\LevelCollection;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use App\Services\LevelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LevelController extends Controller
{
    public function __construct(
        private readonly LevelService $levels,
    ) {
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'username' => $request->user()->username,
                'role' => $request->user()->role?->value,
            ],
        ]);
    }

    public function index(Request $request): LevelCollection
    {
        $search = $request->query('q');

        return new LevelCollection(
            $this->levels->paginate(is_string($search) ? $search : null),
        );
    }

    public function show(Level $level): LevelResource
    {
        return new LevelResource($level);
    }

    public function store(StoreLevelRequest $request): JsonResponse
    {
        $level = $this->levels->create($request->validated(), $request->user());

        return (new LevelResource($level))
            ->response()
            ->setStatusCode(201)
            ->header('Location', route('api.v1.levels.show', ['level' => $level]));
    }

    public function update(UpdateLevelRequest $request, Level $level): LevelResource
    {
        return new LevelResource($this->levels->update($level, $request->validated()));
    }

    public function destroy(Level $level): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $level);
        $this->levels->delete($level);

        return response()->noContent();
    }
}
