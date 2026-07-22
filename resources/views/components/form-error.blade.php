@props(['name'])

@error($name)
<p 
    id="{{ $name }}-error" 
    class="text-sm text-danger mt-1" 
    role="alert" 
    aria-live="polite"
>
    {{ $message }}
</p>
@enderror
