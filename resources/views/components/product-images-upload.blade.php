@props([
    'title' => 'Fotos do produto',
    'description' => 'Envie ate 3 imagens. A primeira sera a principal.',
    'currentPrimary' => null,
    'currentSecondary' => null,
    'currentThird' => null,
    'primaryPosition' => 'center',
    'secondaryPosition' => 'center',
    'thirdPosition' => 'center',
    'border' => '#e5e7eb',
    'primary' => '#db6b96',
    'background' => '#ffffff',
    'card' => '#ffffff',
    'muted' => '#6b7280',
    'listen' => false,
])

<div
    x-data="{
        isDragging: false,
        isPositioning: false,
        isMovingPhoto: false,
        active: 0,
        labels: ['Imagem principal', 'Segunda imagem', 'Terceira imagem'],
        names: ['', '', ''],
        order: ['image', 'image_2', 'image_3'],
        previews: [@js($currentPrimary), @js($currentSecondary), @js($currentThird)],
        positions: [@js($primaryPosition ?: 'center'), @js($secondaryPosition ?: 'center'), @js($thirdPosition ?: 'center')],
        init() {
            this.positions = this.positions.map((position) => this.normalizePosition(position));
            this.$nextTick(() => {
                this.syncHiddenFields();

                const form = this.$root.closest('form');
                if (form) {
                    form.addEventListener('submit', () => this.syncHiddenFields(), true);
                }
            });

            if (!@js($listen)) return;

            window.addEventListener('product-images:load', (event) => {
                this.previews = event.detail.images || [null, null, null];
                this.positions = (event.detail.positions || ['center', 'center', 'center'])
                    .map((position) => this.normalizePosition(position));
                this.names = ['', '', ''];
                this.order = ['image', 'image_2', 'image_3'];
                this.active = this.previews.findIndex((preview) => preview);
                this.active = this.active === -1 ? 0 : this.active;
                this.isPositioning = false;
                this.isMovingPhoto = false;
                this.assignInput('primaryInput', null);
                this.assignInput('secondaryInput', null);
                this.assignInput('thirdInput', null);
                this.syncHiddenFields();
            });
        },
        syncHiddenFields() {
            if (!this.$refs.primaryPosition) return;

            this.$refs.primaryPosition.value = this.normalizePosition(this.positions[0]);
            this.$refs.secondaryPosition.value = this.normalizePosition(this.positions[1]);
            this.$refs.thirdPosition.value = this.normalizePosition(this.positions[2]);
            this.$refs.imageOrder.value = this.order.join(',');
        },
        normalizePosition(position) {
            if (position === 'top') return '50% 0%';
            if (position === 'bottom') return '50% 100%';
            if (position === 'center' || !position) return '50% 50%';

            return /^\d{1,3}% \d{1,3}%$/.test(position) ? position : '50% 50%';
        },
        positionCss(index) {
            this.positions[index] = this.normalizePosition(this.positions[index]);

            return this.positions[index];
        },
        choose() {
            if (this.isPositioning) return;

            this.$refs.gallery.click();
        },
        chooseSlot(index) {
            if (index === 0) this.$refs.primaryPicker.click();
            if (index === 1) this.$refs.secondaryPicker.click();
            if (index === 2) this.$refs.thirdPicker.click();
        },
        centerPhoto(index) {
            this.positions[index] = '50% 50%';
            this.syncHiddenFields();
        },
        startPositioning(event) {
            if (!this.isPositioning || !this.previews[this.active]) return;
            if (event.target.closest('[data-image-control]')) return;

            this.isMovingPhoto = true;
            this.updatePosition(event);
        },
        movePosition(event) {
            if (!this.isMovingPhoto) return;

            this.updatePosition(event);
        },
        stopPositioning() {
            this.isMovingPhoto = false;
        },
        updatePosition(event) {
            const rect = event.currentTarget.getBoundingClientRect();
            const point = event.touches ? event.touches[0] : event;
            const x = Math.max(0, Math.min(100, Math.round(((point.clientX - rect.left) / rect.width) * 100)));
            const y = Math.max(0, Math.min(100, Math.round(((point.clientY - rect.top) / rect.height) * 100)));

            this.positions[this.active] = `${x}% ${y}%`;
            this.syncHiddenFields();
        },
        makePrincipal(index) {
            if (index === 0 || !this.previews[index]) return;

            this.swapSlots(0, index);
            this.active = 0;
        },
        swapSlots(first, second) {
            [this.previews[first], this.previews[second]] = [this.previews[second], this.previews[first]];
            [this.positions[first], this.positions[second]] = [this.positions[second], this.positions[first]];
            [this.names[first], this.names[second]] = [this.names[second], this.names[first]];
            [this.order[first], this.order[second]] = [this.order[second], this.order[first]];
            this.swapFileInputs(first, second);
            this.syncHiddenFields();
        },
        swapFileInputs(first, second) {
            const refs = ['primaryInput', 'secondaryInput', 'thirdInput'];
            const firstFile = this.$refs[refs[first]].files[0] || null;
            const secondFile = this.$refs[refs[second]].files[0] || null;

            this.assignInput(refs[first], secondFile);
            this.assignInput(refs[second], firstFile);
        },
        assignInput(ref, file) {
            const transfer = new DataTransfer();

            if (file) transfer.items.add(file);

            this.$refs[ref].files = transfer.files;
        },
        setFiles(files) {
            if (!files || !files.length) return;

            const images = [...files]
                .filter((file) => file.type && file.type.startsWith('image/'))
                .slice(0, 3);

            this.order = ['image', 'image_2', 'image_3'];
            this.setSlotFile(0, images[0] || null);
            this.setSlotFile(1, images[1] || null);
            this.setSlotFile(2, images[2] || null);
            this.active = images[0] ? 0 : this.active;
            this.syncHiddenFields();
        },
        setSingle(index, files) {
            if (!files || !files.length) return;

            const file = [...files].find((item) => item.type && item.type.startsWith('image/'));

            if (!file) return;

            this.setSlotFile(index, file);
            this.active = index;
        },
        setSlotFile(index, file) {
            const refs = ['primaryInput', 'secondaryInput', 'thirdInput'];

            if (!file) {
                this.assignInput(refs[index], null);
                return;
            }

            this.names[index] = file.name;
            this.previews[index] = URL.createObjectURL(file);
            this.positions[index] = this.normalizePosition(this.positions[index]);
            this.assignInput(refs[index], file);
            this.syncHiddenFields();
        },
    }"
    class="space-y-3"
>
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="font-bold text-xl">{{ $title }}</h2>
            <p class="text-sm" style="color: {{ $muted }}">{{ $description }}</p>
        </div>

        <button
            type="button"
            @click="choose()"
            class="hidden sm:inline-flex items-center gap-2 rounded-2xl px-4 py-3 text-white font-bold"
            style="background: {{ $primary }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 16V4"></path>
                <path d="m7 9 5-5 5 5"></path>
                <path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"></path>
            </svg>
            Enviar imagens
        </button>
    </div>

    <div
        role="button"
        tabindex="0"
        @click="choose()"
        @keydown.enter.prevent="choose()"
        @keydown.space.prevent="choose()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; setFiles($event.dataTransfer.files)"
        class="rounded-3xl border p-3 md:p-4 cursor-pointer transition focus:outline-none focus:ring-4"
        :class="isDragging ? 'scale-[1.01]' : ''"
        style="background: {{ $card }}; border-color: {{ $border }};"
        :style="isDragging ? 'background: {{ $card }}; border-color: {{ $primary }}; box-shadow: 0 0 0 4px {{ $primary }}22;' : 'background: {{ $card }}; border-color: {{ $border }};'"
    >
        <input x-ref="gallery" type="file" accept="image/*" multiple class="sr-only" @click.stop @change="setFiles($event.target.files)">

        <input x-ref="primaryInput" type="file" name="image" accept="image/*" class="sr-only">
        <input x-ref="secondaryInput" type="file" name="image_2" accept="image/*" class="sr-only">
        <input x-ref="thirdInput" type="file" name="image_3" accept="image/*" class="sr-only">

        <input x-ref="primaryPosition" type="hidden" name="image_position" value="{{ $primaryPosition ?: 'center' }}">
        <input x-ref="secondaryPosition" type="hidden" name="image_2_position" value="{{ $secondaryPosition ?: 'center' }}">
        <input x-ref="thirdPosition" type="hidden" name="image_3_position" value="{{ $thirdPosition ?: 'center' }}">
        <input x-ref="imageOrder" type="hidden" name="image_order" value="image,image_2,image_3">

        <input x-ref="primaryPicker" type="file" accept="image/*" class="sr-only" @click.stop @change="setSingle(0, $event.target.files)">
        <input x-ref="secondaryPicker" type="file" accept="image/*" class="sr-only" @click.stop @change="setSingle(1, $event.target.files)">
        <input x-ref="thirdPicker" type="file" accept="image/*" class="sr-only" @click.stop @change="setSingle(2, $event.target.files)">

        <div class="rounded-3xl border overflow-hidden" style="background: {{ $background }}; border-color: {{ $border }};">
            <div
                class="relative h-56 md:h-72 flex items-center justify-center select-none"
                :class="isPositioning && previews[active] ? 'cursor-move' : ''"
                @click.stop="!previews[active] ? choose() : null"
                @pointerdown.prevent="startPositioning($event)"
                @pointermove.prevent="movePosition($event)"
                @pointerup="stopPositioning()"
                @pointercancel="stopPositioning()"
                @pointerleave="stopPositioning()"
            >
                <template x-if="previews[active]">
                    <img :src="previews[active]" alt="" class="h-full w-full object-cover pointer-events-none" :style="`object-position: ${positionCss(active)};`">
                </template>

                <div x-show="!previews[active]" class="text-center px-6">
                    <div class="h-16 w-16 mx-auto rounded-2xl flex items-center justify-center" style="background: {{ $primary }}18; color: {{ $primary }};">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                            <path d="m8 14 2.5-2.5L14 15l2-2 3 3"></path>
                            <circle cx="9" cy="9" r="1.5"></circle>
                        </svg>
                    </div>

                    <p class="font-bold mt-4" style="color: {{ $primary }}">Enviar imagens</p>
                    <p class="text-sm mt-1" style="color: {{ $muted }}">Clique aqui ou arraste ate 3 fotos.</p>
                </div>

                <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-xs font-bold text-white" style="background: {{ $primary }}">
                    <span x-text="labels[active]"></span>
                </span>

                <div
                    data-image-control
                    class="absolute right-3 top-3 rounded-2xl bg-white/90 shadow p-1 flex gap-1"
                    x-show="previews[active]"
                    @click.stop
                    @pointerdown.stop
                    @pointermove.stop
                    @pointerup.stop
                >
                    <button type="button" class="h-9 w-9 rounded-xl flex items-center justify-center" :style="isPositioning ? 'background: {{ $primary }}; color: white;' : 'color: {{ $primary }};'" @click.stop="isPositioning = !isPositioning; stopPositioning(); syncHiddenFields()" title="Mover foto">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20"></path>
                            <path d="M2 12h20"></path>
                            <path d="m15 5-3-3-3 3"></path>
                            <path d="m15 19-3 3-3-3"></path>
                            <path d="m5 9-3 3 3 3"></path>
                            <path d="m19 9 3 3-3 3"></path>
                        </svg>
                    </button>

                    <button type="button" class="h-9 w-9 rounded-xl flex items-center justify-center" style="color: {{ $primary }};" @click.stop="centerPhoto(active)" title="Centralizar foto">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                    </button>

                    <button type="button" x-show="active !== 0" class="h-9 px-3 rounded-xl flex items-center gap-2 font-bold text-sm" style="color: {{ $primary }};" @click.stop="makePrincipal(active)" title="Tornar principal">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3 6 6 .9-4.5 4.4 1.1 6.2L12 16.5 6.4 19.5l1.1-6.2L3 8.9 9 8z"></path>
                        </svg>
                        Principal
                    </button>
                </div>

                <div
                    data-image-control
                    x-show="isPositioning && previews[active]"
                    class="absolute left-3 bottom-3 right-3 rounded-2xl bg-white/90 px-4 py-3 text-sm font-semibold shadow"
                    style="color: {{ $primary }}"
                    @click.stop
                    @pointerdown.stop
                    @pointermove.stop
                    @pointerup.stop
                >
                    Arraste a foto para enquadrar melhor.
                </div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-3">
            <template x-for="index in [0, 1, 2]" :key="index">
                <button
                    type="button"
                    @click.stop="previews[index] ? active = index : chooseSlot(index)"
                    class="relative h-20 rounded-2xl border overflow-hidden flex items-center justify-center transition"
                    :style="active === index ? 'border-color: {{ $primary }}; box-shadow: 0 0 0 3px {{ $primary }}22; background: {{ $background }};' : 'border-color: {{ $border }}; background: {{ $background }};'"
                >
                    <template x-if="previews[index]">
                        <img :src="previews[index]" alt="" class="h-full w-full object-cover" :style="`object-position: ${positionCss(index)};`">
                    </template>

                    <span
                        x-show="index === 0 && previews[index]"
                        class="absolute left-2 top-2 rounded-full px-2 py-1 text-[10px] font-bold text-white"
                        style="background: {{ $primary }}"
                    >
                        Principal
                    </span>

                    <div x-show="!previews[index]" class="h-11 w-11 rounded-xl flex items-center justify-center" style="background: {{ $primary }}18; color: {{ $primary }};">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>
                    </div>
                </button>
            </template>
        </div>

        <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold" style="color: {{ $muted }};">
            <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">JPG</span>
            <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">PNG</span>
            <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">WEBP</span>
            <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">Ate 3 imagens</span>
            <span class="rounded-full border px-3 py-1" style="border-color: {{ $border }};">Arraste para enquadrar</span>
        </div>
    </div>
</div>
