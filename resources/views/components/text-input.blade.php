@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'p-2.5 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400']) !!}>
