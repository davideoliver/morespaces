@extends('layouts.app')

@section('title', 'MoreSpaces | Erro interno')

@section('content')
<div class="page-shell files-page">
  <main class="files-not-found" role="main">
    <p class="eyebrow">Erro 500</p>
    <h1>Este espaço encontrou uma instabilidade.</h1>
    <p>{{ $message }}</p>
    <div class="files-not-found__actions">
      <a class="button-link" href="{{ route('home') }}#home">Voltar para Home</a>
    </div>
  </main>
</div>
@endsection
