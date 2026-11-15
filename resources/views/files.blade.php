@extends('layouts.app')

@section('title', 'MoreSpaces | Arquivos')

@section('content')
<div class="page-shell files-page">
  <x-site-header files-page />
  <main class="files-content">
    <section class="files-hero">
      <p class="eyebrow">M.G.E</p>
      <h1>Arquivos</h1>
      <p>Bem vindo agente,<br>
      Selecione um nível para consultar os arquivos do Maior Grupo de Exploradores (M.G.E) disponíveis para cada espaço.</p>.<br>
      <p class="note">Nota: Os arquivos podem ser acessados apenas por usuários autenticados.</p>
      <br>
    </section>
    <section class="files-levels" aria-labelledby="files-levels-title">
      <header>
        <p class="eyebrow">Catálogo</p>
        <h2 id="files-levels-title">Níveis disponíveis</h2>
      </header>
      <div class="files-level-grid">
        @foreach ($levels as $level)
        <a class="files-level-link" href="{{ route('files.level', ['level' => $level]) }}">
          <span class="files-level-link__number">{{ $level }}</span>
          <span><strong>Nível {{ $level }}</strong><small>Explorar arquivos</small></span>
        </a>
        @endforeach
      </div>
    </section>
  </main>
</div>
@endsection