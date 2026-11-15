@extends('layouts.app')

@section('title', 'MoreSpaces | Mensagens')

@section('content')
<div class="page-shell files-page">
  <x-site-header files-page />
  <x-flash-messages />
  <main class="files-content">
    <section class="files-hero">
      <p class="eyebrow">Administração</p>
      <h1>Mensagens recebidas</h1>
      <p>Consulte as mensagens enviadas pelo formulário de contato.</p>
    </section>
    <section class="files-levels" aria-labelledby="messages-title">
      <header>
        <p class="eyebrow">Contato</p>
        <h2 id="messages-title">Mensagens</h2>
      </header>
      <div>
        @forelse ($messages as $message)
        <article>
          <h3>{{ $message->name }}</h3>
          <p><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></p>
          <p>{{ $message->message }}</p>
          <small>{{ $message->created_at?->format('d/m/Y H:i') }}</small>
        </article>
        @empty
        <p>Nenhuma mensagem recebida.</p>
        @endforelse
      </div>
      {{ $messages->links() }}
    </section>
  </main>
</div>
@endsection
