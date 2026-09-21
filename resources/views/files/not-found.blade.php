@extends('layouts.app')

@section('title', 'MoreSpaces | Arquivo não encontrado')

@section('content')
<div class="page-shell files-page">
  {{-- Renderizada pelo HomeController::file() com status HTTP 404
       sempre que o nível solicitado ainda não tem conteúdo real
       (nenhuma integração com banco de dados foi feita até o momento). --}}
  <main class="files-not-found">
    <p class="eyebrow">Arquivo {{ $level }}</p>
    <h1>Este arquivo ainda não foi encontrado.</h1>
    <p>O nível solicitado ficará disponível quando os arquivos forem conectados ao banco de dados.</p>
    <div class="files-not-found__actions">
      <a class="button-link" href="{{ route('files') }}">Voltar para Arquivos</a>
      <a href="{{ route('home') }}#home">Ir para Home</a>
    </div>
  </main>
</div>
@endsection
