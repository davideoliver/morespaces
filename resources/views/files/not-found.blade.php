@extends('layouts.app')

@section('title', 'MoreSpaces | Arquivo não encontrado')

@section('content')
<div class="page-shell files-page">
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