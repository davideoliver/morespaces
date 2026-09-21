<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="A MoreSpaces é uma companhia que cria espaços para ideias, projetos e pessoas.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- Controla quanta informação de referência (referrer) é enviada ao navegar
       para links externos; "strict-origin-when-cross-origin" é o equilíbrio
       recomendado entre privacidade e funcionalidade. --}}
  <meta name="referrer" content="strict-origin-when-cross-origin">
  {{-- @yield permite que cada view filha (home, files, not-found) defina
       seu próprio título de página; caso não defina, usa o valor padrão. --}}
  <title>@yield('title', 'MoreSpaces | Home')</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('public/imgs/favcons/favicon.ico') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/imgs/favcons/favicon-16x16.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('public/imgs/favcons/favicon-32x32.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/imgs/favcons/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('public/imgs/favcons/android-chrome-192x192.png') }}">
  <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('public/imgs/favcons/android-chrome-512x512.png') }}">
  <link rel="manifest" href="{{ asset('public/imgs/favcons/site.webmanifest') }}">
  <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
  {{-- "defer" garante que o script só execute depois que todo o HTML
       tiver sido interpretado, sem bloquear o carregamento da página. --}}
  <script src="{{ asset('public/js/scripts.js') }}" defer></script>
</head>
<body>
  {{-- Todo o conteúdo específico de cada página (home, files,
       not-found) é injetado aqui através da diretiva @section('content'). --}}
  @yield('content')
</body>
</html>
