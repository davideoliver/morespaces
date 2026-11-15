@csrf
@if ($level->exists)
@method('PUT')
@endif
<div>
  <label for="number">Número</label>
  <input id="number" name="number" type="number" min="0" max="9999"
         value="{{ old('number', $level->number) }}">
  @error('number')<p class="form-message is-error">{{ $message }}</p>@enderror
</div>
<div>
  <label for="name">Nome</label>
  <input id="name" name="name" type="text" minlength="3" maxlength="120" required
         value="{{ old('name', $level->name) }}">
  @error('name')<p class="form-message is-error">{{ $message }}</p>@enderror
</div>
<div>
  <label for="summary">Resumo</label>
  <input id="summary" name="summary" type="text" maxlength="255"
         value="{{ old('summary', $level->summary) }}">
  @error('summary')<p class="form-message is-error">{{ $message }}</p>@enderror
</div>
<div>
  <label for="description">Descrição</label>
  <textarea id="description" name="description" maxlength="5000" required>{{ old('description', $level->description) }}</textarea>
  @error('description')<p class="form-message is-error">{{ $message }}</p>@enderror
</div>
<div>
  <label for="danger_level">Nível de perigo</label>
  <select id="danger_level" name="danger_level" required>
    @foreach (\App\Enums\DangerLevel::cases() as $dangerLevel)
    <option value="{{ $dangerLevel->value }}" @selected(old('danger_level', $level->danger_level?->value) === $dangerLevel->value)>
      {{ ucfirst($dangerLevel->value) }}
    </option>
    @endforeach
  </select>
  @error('danger_level')<p class="form-message is-error">{{ $message }}</p>@enderror
</div>
<button type="submit">{{ $level->exists ? 'Salvar alterações' : 'Criar nível' }}</button>
