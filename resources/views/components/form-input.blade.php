@props(['name', 'label', 'type' => 'text', 'required' => false, 'class' => ''])

<div>
    <label for="{{ $name }}" class="label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name) }}"
        {{ $required ? 'required' : '' }}
        @error($name)
            aria-invalid="true"
            aria-describedby="{{ $name }}-error"
        @enderror
        {!! $attributes->merge(['class' => 'input-field ' . ($errors->has($name) ? 'border-danger' : '') . ($class ? ' ' . $class : '')]) !!}
    >
    <x-form-error :name="$name" />
</div>
