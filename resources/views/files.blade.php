@extends('layouts.app')

@section('title', 'MoreSpaces | Arquivos')

@section('content')
<div class="page-shell files-page">
  <x-site-header files-page />
  <x-flash-messages />
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
        @if ($search !== null && $search !== '')
        <p>Resultados para: <strong>{{ $search }}</strong></p>
        @endif
        <form method="GET" action="{{ route('files.index') }}" role="search">
          <label for="level-search">Buscar arquivos</label>
          <input id="level-search" name="q" value="{{ $search }}" maxlength="120" type="search"
                 placeholder="Nome, resumo ou descrição">
          <button type="submit">Buscar</button>
        </form>
        @can('create', \App\Models\Level::class)
        <a class="button-link" href="{{ route('files.create') }}">Novo nível</a>
        @endcan
      </header>
      <div class="files-level-grid">
        @forelse ($levels as $level)
        <a class="files-level-link" href="{{ route('files.show', ['level' => $level]) }}">
          <span class="files-level-link__number">{{ $level->number }}</span>
          <span><strong>{{ $level->name }}</strong><small>{{ $level->summary ?: 'Explorar arquivo' }}</small></span>
        </a>
        @empty
        <div class="files-empty">
          <div>
            <p class="eyebrow">Catálogo vazio</p>
            <h1>Nenhum arquivo encontrado.</h1>
            <p>Tente outro termo de busca ou cadastre um novo nível.</p>
          </div>
        </div>
        @endforelse
      </div>
      {{ $levels->withQueryString()->links() }}
    </section>
  </main>
</div>
@endsection