@extends('layouts.app')

@section('title', 'MoreSpaces | Acesso restrito')

@section('content')
<div class="page-shell files-page">
  <main class="files-empty" role="main">
    <div>
      <p class="eyebrow">Acesso restrito</p>
      <h1>Não há arquivos disponíveis.</h1>
      <p>Você não tem permissão para acessar este espaço. Continue acompanhando a MoreSpaces para saber quando novos espaços forem liberados.</p>
      <a class="button-link" href="{{ route('home') }}#home">Voltar para Home</a>
    </div>
  </main>
</div>
@endsection
