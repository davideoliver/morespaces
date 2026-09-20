<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="A MoreSpaces é uma companhia que cria espaços para ideias, projetos e pessoas.">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="referrer" content="strict-origin-when-cross-origin">
  <title>@yield('title', 'MoreSpaces | Home')</title>
  <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
  <script src="{{ asset('public/js/scripts.js') }}" defer></script>
</head>
<body>
  @yield('content')
</body>
</html>