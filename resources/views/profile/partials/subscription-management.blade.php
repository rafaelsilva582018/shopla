@php
    $theme = auth()->user()?->store?->dashboardTheme() ?? config('dashboard-themes.blush');
    $currentPlan = $user->plan ?: 'free';
    $plan = $plans[$currentPlan] ?? $plans['free'];
    $latestSubscription = $latestSubscription ?? null;
    $activeSubscription = $activeSubscription ?? null;
    $isPaid = $currentPlan !== 'free';
@endphp

<section>
    <header class="flex items-start gap-4">
        <span class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
            <x-dashboard-icon name="diamond" class="w-6 h-6" />
        </span>

        <div>
            <h2 class="text-xl font-bold">
                Assinatura
            </h2>

            <p class="mt-1 text-sm" style="color: {{ $theme['muted'] }}">
                Veja seu plano atual, continue um pagamento pendente ou cancele uma assinatura ativa.
            </p>
        </div>
    </header>

    <div class="mt-6 grid lg:grid-cols-[1fr_.9fr] gap-5">
        <div class="rounded-3xl border p-5" style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}">
            <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">
                Plano atual
            </p>

            <div class="mt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h3 class="text-3xl font-black">
                        {{ $plan['name'] }}
                    </h3>

                    <p class="mt-2 text-sm" style="color: {{ $theme['muted'] }}">
                        Limite de {{ $plan['limit'] ? $plan['limit'] . ' produtos' : 'produtos ilimitados' }}.
                        {{ ($plan['custom_slug'] ?? false) ? 'Link personalizado liberado.' : 'Link gerado automaticamente.' }}
                    </p>
                </div>

                @if(!$isPaid)
                    <a
                        href="{{ route('plans.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl px-5 py-3 font-semibold text-white"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Ver planos
                    </a>
                @else
                    <a
                        href="{{ route('plans.index') }}"
                        class="inline-flex items-center justify-center rounded-2xl px-5 py-3 font-semibold border"
                        style="border-color: {{ $theme['border'] }}; color: {{ $theme['primary'] }}"
                    >
                        Alterar plano
                    </a>
                @endif
            </div>
        </div>

        <div class="rounded-3xl border p-5" style="border-color: {{ $theme['border'] }}">
            @if($activeSubscription)
                <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">
                    Assinatura ativa
                </p>

                <p class="mt-3 font-bold">
                    Plano {{ $plans[$activeSubscription->plan]['name'] ?? $activeSubscription->plan }}
                </p>

                <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                    Confirmada em {{ $activeSubscription->paid_at?->format('d/m/Y') ?? $activeSubscription->created_at?->format('d/m/Y') }}.
                </p>

                <form method="POST" action="{{ route('subscription.cancel') }}" class="mt-5" onsubmit="return confirm('Tem certeza que deseja cancelar sua assinatura? Seu plano voltara para o gratuito.')">
                    @csrf
                    @method('DELETE')

                    <button class="w-full rounded-2xl px-5 py-3 font-semibold border border-red-100 bg-red-50 text-red-600">
                        Cancelar assinatura
                    </button>
                </form>
            @elseif($latestSubscription && $latestSubscription->status === 'pending')
                <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">
                    Pagamento pendente
                </p>

                <p class="mt-3 font-bold">
                    Plano {{ $plans[$latestSubscription->plan]['name'] ?? $latestSubscription->plan }}
                </p>

                <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                    O checkout foi criado, mas o pagamento ainda nao foi confirmado.
                </p>

                @if($latestSubscription->checkout_url)
                    <a
                        href="{{ $latestSubscription->checkout_url }}"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-2xl px-5 py-3 font-semibold text-white"
                        style="background: {{ $theme['primary'] }}"
                    >
                        Continuar pagamento
                    </a>
                @endif
            @elseif($isPaid)
                <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">
                    Plano liberado
                </p>

                <p class="mt-3 font-bold">
                    Seu plano esta ativo na conta.
                </p>

                <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                    Nao encontrei uma assinatura ativa do Asaas para cancelamento automatico. Se precisar cancelar, fale com o suporte.
                </p>
            @else
                <p class="text-xs font-bold uppercase tracking-widest" style="color: {{ $theme['muted'] }}">
                    Sem assinatura paga
                </p>

                <p class="mt-3 font-bold">
                    Voce esta usando o plano gratuito.
                </p>

                <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                    Quando assinar um plano, os controles de assinatura aparecem aqui.
                </p>
            @endif
        </div>
    </div>
</section>
