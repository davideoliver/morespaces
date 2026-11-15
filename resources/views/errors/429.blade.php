@extends('layouts.app')

@section('title', 'MoreSpaces | Muitas tentativas')

@section('content')
<div class="page-shell files-page">
  <main class="files-not-found" role="main">
    <p class="eyebrow">Aguarde um pouco</p>
    <h1>Há movimento demais neste espaço.</h1>
    <p>{{ $message }}</p>
    <div class="files-not-found__actions">
      <a class="button-link" href="{{ route('home') }}#home">Voltar para Home</a>
    </div>
  </main>
</div>
@endsection
