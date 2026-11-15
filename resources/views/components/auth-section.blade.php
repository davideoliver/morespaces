@props([
    'sectionId',
    'sectionStyle',
    'figureClass',
    'image',
    'imageAlt',
    'caption',
    'imageFirst' => true,
])

<section id="{{ $sectionId }}" class="auth-section" style="{{ $sectionStyle }}">
  @if ($imageFirst)
  <figure class="auth-figure {{ $figureClass }}">
    <img src="{{ asset((request()->getBaseUrl() === '' ? '' : 'public/').'imgs/'.$image) }}?v={{ filemtime(public_path('imgs/'.$image)) }}" alt="{{ $imageAlt }}">
    <figcaption>{{ $caption }}</figcaption>
  </figure>
  @endif
  <div class="auth-content">
    {{ $slot }}
  </div>
  @unless ($imageFirst)
  <figure class="auth-figure {{ $figureClass }}">
    <img src="{{ asset((request()->getBaseUrl() === '' ? '' : 'public/').'imgs/'.$image) }}?v={{ filemtime(public_path('imgs/'.$image)) }}" alt="{{ $imageAlt }}">
    <figcaption>{{ $caption }}</figcaption>
  </figure>
  @endunless
</section>
