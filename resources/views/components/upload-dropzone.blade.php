@props([
    'name',
    'label',
    'hint' => 'Arraste uma imagem aqui ou clique para escolher.',
    'current' => null,
    'border' => '#e5e7eb',
    'primary' => '#db6b96',
    'background' => '#ffffff',
    'muted' => '#6b7280',
    'compact' => false,
    'embedded' => false,
])

<div
    x-data="{
        isDragging: false,
        fileName: '',
        preview: @js($current),
        choose() {
            this.$refs.input.click();
        },
        setFile(files) {
            if (!files || !files.length) {
                return;
            }

            const file = files[0];
            this.fileName = file.name;

            if (file.type && file.type.startsWith('image/')) {
                this.preview = URL.createObjectURL(file);
            }

            const transfer = new DataTransfer();
            transfer.items.add(file);
            this.$refs.input.files = transfer.files;
        },
    }"
    class="space-y-2"
>
    @unless($compact)
        <label class="block text-sm font-semibold">
            {{ $label }}
        </label>
    @endunless

    <div
        role="button"
        tabindex="0"
        @click="choose()"
        @keydown.enter.prevent="choose()"
        @keydown.space.prevent="choose()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; setFile($event.dataTransfer.files)"
        class="group w-full rounded-3xl text-left transition focus:outline-none focus:ring-4 cursor-pointer {{ $compact ? 'p-3' : 'p-4' }} {{ $embedded ? 'border-0' : 'border-2 border-dashed' }}"
        :class="isDragging ? 'scale-[1.01]' : ''"
        style="background: {{ $background }}; border-color: {{ $border }};"
        :style="isDragging ? 'background: {{ $background }}; border-color: {{ $primary }}; box-shadow: 0 0 0 4px {{ $primary }}22;' : 'background: {{ $background }}; border-color: {{ $border }};'"
    >
        <input
            x-ref="input"
            type="file"
            name="{{ $name }}"
            accept="image/*"
            class="sr-only"
            @click.stop
            @change="setFile($event.target.files)"
            {{ $attributes }}
        >

        <div class="{{ $compact ? 'space-y-3' : 'flex flex-col sm:flex-row gap-4 sm:items-center' }}">
            <div
                class="relative shrink-0 overflow-hidden rounded-2xl border {{ $compact ? 'h-32 w-full' : 'h-28 w-full sm:w-32' }}"
                style="border-color: {{ $border }}; background: {{ $background }};"
            >
                <template x-if="preview">
                    <img :src="preview" alt="" class="h-full w-full object-cover">
                </template>

                <div x-show="!preview" class="h-full w-full flex items-center justify-center">
                    <div class="h-14 w-14 rounded-2xl flex items-center justify-center" style="background: {{ $primary }}18; color: {{ $primary }};">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                            <path d="m8 14 2.5-2.5L14 15l2-2 3 3"></path>
                            <circle cx="9" cy="9" r="1.5"></circle>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="min-w-0 flex-1">
                @if($compact)
                    <p class="mb-1 text-sm font-semibold">
                        {{ $label }}
                    </p>
                @endif

                <div class="flex items-center gap-2 font-bold" style="color: {{ $primary }};">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 16V4"></path>
                        <path d="m7 9 5-5 5 5"></path>
                        <path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"></path>
                    </svg>
                    <span x-text="fileName || 'Enviar imagem'"></span>
                </div>

                <p class="mt-1 text-sm {{ $compact ? 'leading-snug' : '' }}" style="color: {{ $muted }};">
                    {{ $hint }}
                </p>

                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold" style="color: {{ $muted }};">
                    <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">JPG</span>
                    <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">PNG</span>
                    <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">WEBP</span>
                    <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">Otimiza automático</span>
                </div>
            </div>
        </div>
    </div>
</div>
