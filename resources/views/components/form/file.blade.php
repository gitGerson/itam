@props([
    'name',
    'label' => null,
    'required' => false,
    'help' => null,
    'id' => null,
    'inputClass' => null,
    'groupClass' => null,
    'accept' => null,
    'preview' => null,
    'previewLabel' => 'Current file',
])

@php
    $fieldId = $id ?: preg_replace('/[^A-Za-z0-9\-_:.]/', '_', $name);
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $isInvalid = $errorBag->has($name);
@endphp

<x-form.group :name="$name" :label="$label" :required="$required" :help="$help" :for="$fieldId" :class="$groupClass">
    <input
        type="file"
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @if ($required) required @endif
        @if ($accept) accept="{{ $accept }}" @endif
        {{ $attributes->class(['form-control', $inputClass, 'is-invalid' => $isInvalid]) }}
    >

    @if ($preview)
        <div class="mt-2">
            <small class="text-muted d-block">{{ $previewLabel }}</small>
            <a href="{{ $preview }}" target="_blank" rel="noopener noreferrer">{{ $preview }}</a>
        </div>
    @endif
</x-form.group>
