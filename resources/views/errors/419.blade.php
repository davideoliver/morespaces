@extends('layouts.app')

@section('title', 'MoreSpaces | Sessão expirada')

@section('content')
<div class="page-shell files-page">
  <main class="files-not-found" role="main">
    <p class="eyebrow">Sessão expirada</p>
    <h1>Precisamos atualizar este espaço.</h1>
    <p>{{ $message }}</p>
    <div class="files-not-found__actions">
      <a class="button-link" href="{{ route('home') }}#home">Voltar para Home</a>
    </div>
  </main>
</div>
@endsection
