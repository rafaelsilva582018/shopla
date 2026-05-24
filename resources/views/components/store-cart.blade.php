<div>
    @php
        $sf = $storefrontTheme;
    @endphp

    <button
        wire:click="openCart"
        class="relative h-14 w-14 rounded-2xl shadow-lg flex items-center justify-center transition hover:-translate-y-0.5"
        style="background: {{ $sf['card'] }}; color: {{ $sf['primary'] }}"
        aria-label="Abrir carrinho"
    >
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 6h15l-1.5 9h-12z"/>
            <path d="M6 6 5 3H2"/>
            <circle cx="9" cy="20" r="1"/>
            <circle cx="18" cy="20" r="1"/>
        </svg>

        @if($this->itemsCount > 0)
            <span
                class="absolute -right-2 -top-2 min-w-6 h-6 px-2 rounded-full text-white text-xs font-bold flex items-center justify-center"
                style="background: {{ $sf['primary'] }}"
            >
                {{ $this->itemsCount }}
            </span>
        @endif
    </button>

    @if($this->itemsCount > 0)
        <button
            wire:click="openCart"
            class="fixed left-1/2 bottom-6 z-40 -translate-x-1/2 w-[min(92vw,560px)] text-white rounded-2xl shadow-2xl px-6 py-4 flex items-center justify-between font-bold"
            style="background: {{ $sf['primary'] }}"
        >
            <span class="flex items-center gap-3">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6h15l-1.5 9h-12z"/>
                    <path d="M6 6 5 3H2"/>
                    <circle cx="9" cy="20" r="1"/>
                    <circle cx="18" cy="20" r="1"/>
                </svg>
                {{ $this->itemsCount }} {{ $this->itemsCount === 1 ? 'item' : 'itens' }}
            </span>

            <span>
                R$ {{ number_format($this->total, 2, ',', '.') }}
            </span>
        </button>
    @endif

    @if($open)
        <div class="shopla-scrollbar fixed inset-0 z-50 bg-black/55 px-4 py-6 overflow-y-auto" style="--shopla-scrollbar-thumb: {{ $sf['primary'] }}">
            <div
                class="absolute inset-0"
                wire:click="closeCart"
            ></div>

            <div class="shopla-scrollbar relative mx-auto w-full max-w-2xl max-h-[92vh] overflow-y-auto rounded-3xl shadow-2xl" style="--shopla-scrollbar-thumb: {{ $sf['primary'] }}; background: {{ $sf['card'] }}">
                <div class="sticky top-0 z-10 border-b px-5 md:px-6 py-5 flex items-center justify-between" style="background: {{ $sf['card'] }}; border-color: {{ $sf['border'] }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="h-11 w-11 rounded-2xl flex items-center justify-center" style="background: {{ $sf['secondary'] }}; color: {{ $sf['primary'] }}">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 6h15l-1.5 9h-12z"/>
                                <path d="M6 6 5 3H2"/>
                                <circle cx="9" cy="20" r="1"/>
                                <circle cx="18" cy="20" r="1"/>
                            </svg>
                        </span>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="text-xl md:text-2xl font-bold truncate" style="color: {{ $sf['text'] }}">
                                    Meu carrinho
                                </h2>

                                @if($this->itemsCount > 0)
                                    <span
                                        class="min-w-7 h-7 px-2 rounded-full text-white text-sm font-bold flex items-center justify-center"
                                        style="background: {{ $sf['primary'] }}"
                                    >
                                        {{ $this->itemsCount }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm" style="color: {{ $sf['muted'] }}">
                                Confira os itens antes de finalizar.
                            </p>
                        </div>
                    </div>

                    <button
                        wire:click="closeCart"
                        class="h-11 w-11 rounded-2xl flex items-center justify-center"
                        style="background: {{ $sf['secondary'] }}; color: {{ $sf['muted'] }}"
                        aria-label="Fechar carrinho"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-5 md:p-6">
                    @if($stockMessage)
                        <div
                            class="mb-5 rounded-2xl border p-4 text-sm font-semibold flex items-start gap-3"
                            style="background: {{ $sf['badge'] }}; color: {{ $sf['badge_text'] }}; border-color: {{ $sf['border'] }}"
                        >
                            <svg class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                                <path d="M10.3 3.9 2.4 17.5A2 2 0 0 0 4.1 20h15.8a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"></path>
                            </svg>
                            {{ $stockMessage }}
                        </div>
                    @endif

                    @forelse($cart as $item)
                        <div class="rounded-3xl border p-4 mb-4" style="border-color: {{ $sf['border'] }}; background: {{ $sf['secondary'] }}">
                            <div class="flex gap-4">
                                @if($item['image'])
                                    <img
                                        src="{{ asset('storage/' . $item['image']) }}"
                                        class="w-20 h-20 object-cover rounded-2xl"
                                        alt=""
                                    >
                                @else
                                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center" style="background: {{ $sf['card'] }}; color: {{ $sf['muted'] }}">
                                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                                            <path d="m8 14 2.5-2.5L14 15l2-2 3 3"></path>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between gap-3">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-base md:text-lg truncate" style="color: {{ $sf['text'] }}">{{ $item['name'] }}</h3>
                                            <p class="text-sm" style="color: {{ $sf['muted'] }}">
                                                R$ {{ number_format($item['price'], 2, ',', '.') }} cada
                                            </p>
                                        </div>

                                        <strong class="text-base md:text-lg whitespace-nowrap" style="color: {{ $sf['text'] }}">
                                            R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                        </strong>
                                    </div>

                                    <div class="flex items-center justify-between gap-4 mt-4">
                                        <div class="flex items-center gap-2 rounded-full p-1" style="background: {{ $sf['card'] }}">
                                            <button
                                                wire:click="decrease({{ $item['id'] }})"
                                                class="w-9 h-9 rounded-full border flex items-center justify-center"
                                                style="color: {{ $sf['primary'] }}; border-color: {{ $sf['border'] }}"
                                                aria-label="Diminuir quantidade"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M5 12h14"></path>
                                                </svg>
                                            </button>

                                            <span class="font-bold min-w-6 text-center">{{ $item['quantity'] }}</span>

                                            <button
                                                wire:click="increase({{ $item['id'] }})"
                                                class="w-9 h-9 rounded-full text-white flex items-center justify-center"
                                                style="background: {{ $sf['primary'] }}"
                                                aria-label="Aumentar quantidade"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 5v14"></path>
                                                    <path d="M5 12h14"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <button
                                            wire:click="remove({{ $item['id'] }})"
                                            class="h-10 w-10 rounded-full flex items-center justify-center"
                                            style="background: {{ $sf['card'] }}; color: #ef4444"
                                            aria-label="Remover item"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 6h18"></path>
                                                <path d="M8 6V4h8v2"></path>
                                                <path d="M19 6l-1 14H6L5 6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="h-16 w-16 mx-auto rounded-3xl flex items-center justify-center" style="background: {{ $sf['secondary'] }}; color: {{ $sf['primary'] }}">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M6 6h15l-1.5 9h-12z"/>
                                    <path d="M6 6 5 3H2"/>
                                    <circle cx="9" cy="20" r="1"/>
                                    <circle cx="18" cy="20" r="1"/>
                                </svg>
                            </div>
                            <p class="mt-4 font-semibold" style="color: {{ $sf['text'] }}">Seu carrinho está vazio.</p>
                            <p class="text-sm mt-1" style="color: {{ $sf['muted'] }}">Escolha um produto para começar o pedido.</p>
                        </div>
                    @endforelse

                    @if($this->itemsCount > 0)
                        <div class="rounded-3xl border p-5 mt-6" style="border-color: {{ $sf['border'] }}">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold" style="color: {{ $sf['muted'] }}">Total do pedido</span>

                                <span class="text-3xl font-bold" style="color: {{ $sf['primary'] }}">
                                    R$ {{ number_format($this->total, 2, ',', '.') }}
                                </span>
                            </div>

                            <p class="text-sm mt-2 flex items-center gap-2" style="color: {{ $sf['muted'] }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M10 17h4V5H2v12h3"></path>
                                    <path d="M14 17h1m4 0h3v-6l-3-4h-5"></path>
                                    <circle cx="7.5" cy="17.5" r="2.5"></circle>
                                    <circle cx="17.5" cy="17.5" r="2.5"></circle>
                                </svg>
                                Frete e prazo combinados pelo WhatsApp.
                            </p>
                        </div>

                        <div class="mt-8 text-center">
                            <h3 class="text-2xl font-bold" style="color: {{ $sf['text'] }}">
                                Finalize seu pedido
                            </h3>

                            <p class="mt-2" style="color: {{ $sf['muted'] }}">
                                Seus dados ajudam a loja a confirmar tudo rapidinho.
                            </p>
                        </div>

                        <div class="mt-6 rounded-3xl border p-5 space-y-4" style="background: {{ $sf['secondary'] }}; border-color: {{ $sf['border'] }}">
                            <h4 class="font-bold flex items-center gap-2" style="color: {{ $sf['primary'] }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21a8 8 0 0 0-16 0"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Dados pessoais
                            </h4>

                            <div>
                                <label class="text-sm font-semibold" style="color: {{ $sf['text'] }}">Nome completo</label>
                                <input
                                    wire:model.live="customer_name"
                                    type="text"
                                    placeholder="Seu nome completo"
                                    class="w-full border rounded-2xl p-4 mt-2"
                                    style="border-color: {{ $sf['border'] }}"
                                >
                                @error('customer_name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="text-sm font-semibold" style="color: {{ $sf['text'] }}">WhatsApp</label>
                                <input
                                    wire:model.live="customer_whatsapp"
                                    type="text"
                                    placeholder="(00) 00000-0000"
                                    class="w-full border rounded-2xl p-4 mt-2"
                                    style="border-color: {{ $sf['border'] }}"
                                >
                                @error('customer_whatsapp') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 rounded-3xl border p-5 space-y-4" style="background: {{ $sf['secondary'] }}; border-color: {{ $sf['border'] }}">
                            <h4 class="font-bold flex items-center gap-2" style="color: {{ $sf['primary'] }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Entrega
                            </h4>

                            <input
                                wire:model.live="customer_address"
                                type="text"
                                placeholder="Endereço de entrega"
                                class="w-full border rounded-2xl p-4"
                                style="border-color: {{ $sf['border'] }}"
                            >
                            @error('customer_address') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-6 rounded-3xl border p-5 space-y-4" style="background: {{ $sf['secondary'] }}; border-color: {{ $sf['border'] }}">
                            <h4 class="font-bold flex items-center gap-2" style="color: {{ $sf['primary'] }}">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
                                </svg>
                                Observações
                            </h4>

                            <textarea
                                wire:model.live="notes"
                                placeholder="Cor, tamanho, personalização, mensagem do presente..."
                                class="w-full border rounded-2xl p-4 min-h-28"
                                style="border-color: {{ $sf['border'] }}"
                            ></textarea>
                            @error('notes') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button
                            type="button"
                            wire:click="confirmOrder"
                            class="w-full mt-6 text-center text-white py-4 rounded-2xl font-bold text-lg inline-flex items-center justify-center gap-2"
                            style="background: {{ $sf['primary'] }}"
                        >
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                            Confirmar pedido
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
