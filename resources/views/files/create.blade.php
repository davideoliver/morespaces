@extends('layouts.app')

@section('title', 'MoreSpaces | Novo nível')

@section('content')
<div class="page-shell files-page">
  <x-site-header files-page />
  <x-flash-messages />
  <main class="files-content">
    <section class="files-levels">
      <p class="eyebrow">M.G.E</p>
      <h1>Novo nível</h1>
      <form method="POST" action="{{ route('files.store') }}">
        @include('files._form')
      </form>
    </section>
  </main>
</div>
@endsection
