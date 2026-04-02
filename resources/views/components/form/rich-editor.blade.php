@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'help' => null,
    'placeholder' => null,
    'id' => null,
    'inputClass' => null,
    'groupClass' => null,
    'old' => true,
    'rows' => 8,
    'minHeight' => '180px',
])

@php
    $fieldId = $id ?: preg_replace('/[^A-Za-z0-9\-_:.]/', '_', $name);
    $resolvedValue = $old ? old($name, $value) : $value;
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $isInvalid = $errorBag->has($name);
@endphp

<x-form.group :name="$name" :label="$label" :required="$required" :help="$help" :for="$fieldId" :class="$groupClass">
    <div class="form-rich-editor-shell">
        <textarea
            id="{{ $fieldId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            data-rich-editor="1"
            data-min-height="{{ $minHeight }}"
            @if ($placeholder !== null) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->class(['form-control form-rich-editor', $inputClass, 'is-invalid' => $isInvalid]) }}
        >{{ $resolvedValue }}</textarea>
    </div>
</x-form.group>

@once
    @push('styles')
        <style>
            .form-rich-editor-shell {
                position: relative;
            }

            .form-rich-editor {
                min-height: 180px;
                resize: vertical;
                line-height: 1.65;
            }
        </style>
    @endpush
@endonce
