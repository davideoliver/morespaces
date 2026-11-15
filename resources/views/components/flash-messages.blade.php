@if (session('success') || $errors->any())
<div class="flash-messages" aria-live="polite">
  @if (session('success'))
  <p class="form-message is-success" role="status">{{ session('success') }}</p>
  @endif
  @if ($errors->any())
  <div class="form-message is-error" role="alert">{{ $errors->first() }}</div>
  @endif
</div>
@endif
