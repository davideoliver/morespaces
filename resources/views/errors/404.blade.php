@extends('layouts.app')

@section('title', 'MoreSpaces | Página não encontrada')

@section('content')
<div class="page-shell files-page">
  <main class="files-not-found" role="main">
    <p class="eyebrow">Erro 404</p>
    <h1>Este espaço não foi encontrado.</h1>
    <p>{{ $message }}</p>
    <div class="files-not-found__actions">
      <a class="button-link" href="{{ route('home') }}#home">Voltar para Home</a>
    </div>
  </main>
</div>
@endsection
