<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-block px-4 py-2 text-white bg-primary-500 hover:bg-primary-600 rounded']) }}>
    {{ $slot }}
</button>
