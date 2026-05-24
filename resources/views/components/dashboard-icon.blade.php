@props([
    'name',
    'class' => 'w-5 h-5',
])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>
    @switch($name)
        @case('home')
            <path d="M3 10.5 12 3l9 7.5" />
            <path d="M5 10v10h14V10" />
            <path d="M9 20v-6h6v6" />
            @break

        @case('plus')
            <path d="M5 12h14" />
            <path d="M12 5v14" />
            @break

        @case('orders')
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
            <path d="M3 6h18" />
            <path d="M16 10a4 4 0 0 1-8 0" />
            @break

        @case('stock')
            <path d="m21 16-9 5-9-5" />
            <path d="m21 12-9 5-9-5" />
            <path d="M3 8l9-5 9 5-9 5Z" />
            @break

        @case('store')
            <path d="M3 9h18" />
            <path d="M4 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9" />
            <path d="m4 9 2-6h12l2 6" />
            <path d="M9 21v-6h6v6" />
            @break

        @case('alert')
            <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
            <path d="M12 9v4" />
            <path d="M12 17h.01" />
            @break

        @case('bell')
            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
            <path d="M13.7 21a2 2 0 0 1-3.4 0" />
            @break

        @case('package')
            <path d="M16.5 9.4 7.5 4.2" />
            <path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
            <path d="M3.3 7 12 12l8.7-5" />
            <path d="M12 22V12" />
            @break

        @case('category')
            <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z" />
            @break

        @case('receipt')
            <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2Z" />
            <path d="M8 7h8" />
            <path d="M8 12h8" />
            <path d="M8 17h5" />
            @break

        @case('wallet')
            <path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z" />
            <path d="M16 12h.01" />
            <path d="M3 9h18" />
            @break

        @case('cancel')
            <circle cx="12" cy="12" r="10" />
            <path d="m15 9-6 6" />
            <path d="m9 9 6 6" />
            @break

        @case('copy')
            <rect width="14" height="14" x="8" y="8" rx="2" />
            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
            @break

        @case('external')
            <path d="M15 3h6v6" />
            <path d="M10 14 21 3" />
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
            @break

        @case('trend')
            <path d="m3 17 6-6 4 4 8-8" />
            <path d="M14 7h7v7" />
            @break

        @case('link')
            <path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7" />
            <path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7" />
            @break

        @case('trophy')
            <path d="M8 21h8" />
            <path d="M12 17v4" />
            <path d="M7 4h10v6a5 5 0 0 1-10 0Z" />
            <path d="M7 7H4a2 2 0 0 0 0 4h3" />
            <path d="M17 7h3a2 2 0 0 1 0 4h-3" />
            @break

        @case('diamond')
            <path d="M6 3h12l4 6-10 12L2 9Z" />
            <path d="m12 21 4-12-4-6-4 6Z" />
            <path d="M2 9h20" />
            @break

        @case('gift')
            <rect x="3" y="8" width="18" height="13" rx="2" />
            <path d="M12 8v13" />
            <path d="M3 12h18" />
            <path d="M7.5 8a2.5 2.5 0 1 1 2.5-2.5V8" />
            <path d="M16.5 8A2.5 2.5 0 1 0 14 5.5V8" />
            @break

        @case('logout')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <path d="m16 17 5-5-5-5" />
            <path d="M21 12H9" />
            @break

        @case('settings')
            <path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.6-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 1 1 7.1 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 1 1 19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1a2 2 0 1 1 0 4H21a1.7 1.7 0 0 0-1.6 1Z" />
            @break

        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
