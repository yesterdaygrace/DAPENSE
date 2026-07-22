@props(['name', 'label', 'options' => [], 'required' => false, 'class' => '', 'placeholder' => null])

<div>
    <label for="{{ $name }}" class="label">
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        @error($name)
            aria-invalid="true"
            aria-describedby="{{ $name }}-error"
        @enderror
        {!! $attributes->merge(['class' => 'select-field ' . ($errors->has($name) ? 'border-danger' : '') . ($class ? ' ' . $class : '')]) !!}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ old($name) == $value ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
        
        {{ $slot }}
    </select>
    <x-form-error :name="$name" />
</div>
