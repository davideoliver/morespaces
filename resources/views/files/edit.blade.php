@extends('layouts.app')

@section('title', 'MoreSpaces | Editar nível')

@section('content')
<div class="page-shell files-page">
  <x-site-header files-page />
  <x-flash-messages />
  <main class="files-content">
    <section class="files-levels">
      <p class="eyebrow">M.G.E</p>
      <h1>Editar nível {{ $level->number }}</h1>
      <form method="POST" action="{{ route('files.update', $level) }}">
        @include('files._form')
      </form>
    </section>
  </main>
</div>
@endsection
