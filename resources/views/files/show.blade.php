@extends('layouts.app')

@section('title', 'MoreSpaces | Nível '.$level->number)

@section('content')
<div class="page-shell files-page">
  <x-site-header files-page />
  <x-flash-messages />
  <main class="files-not-found">
    <p class="eyebrow">Arquivo {{ $level->number }}</p>
    <h1>{{ $level->name }}</h1>
    @if ($level->summary)<p>{{ $level->summary }}</p>@endif
    <p>{{ $level->description }}</p>
    <p><strong>Perigo:</strong> {{ ucfirst($level->danger_level->value) }}</p>
    <div class="files-not-found__actions">
      @can('update', $level)
      <a class="button-link" href="{{ route('files.edit', $level) }}">Editar</a>
      @endcan
      @can('delete', $level)
      <form method="POST" action="{{ route('files.destroy', $level) }}" data-confirm="Excluir este nível?">
        @csrf
        @method('DELETE')
        <button type="submit">Excluir</button>
      </form>
      @endcan
      <a href="{{ route('files.index') }}">Voltar para Arquivos</a>
    </div>
  </main>
</div>
@endsection
