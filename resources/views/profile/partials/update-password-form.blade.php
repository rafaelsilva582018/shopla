@php
    $theme = auth()->user()?->store?->dashboardTheme() ?? config('dashboard-themes.blush');
@endphp

<section>
    <header class="flex items-start gap-4">
        <span class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
            <x-dashboard-icon name="alert" class="w-6 h-6" />
        </span>

        <div>
            <h2 class="text-xl font-bold">
                Segurança
            </h2>

            <p class="mt-1 text-sm" style="color: {{ $theme['muted'] }}">
                Atualize sua senha de acesso quando precisar.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label for="update_password_current_password" class="block text-sm font-semibold mb-2">Senha atual</label>
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full rounded-2xl border px-4 py-3"
                    style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}"
                >
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password" class="block text-sm font-semibold mb-2">Nova senha</label>
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-2xl border px-4 py-3"
                    style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}"
                >
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-semibold mb-2">Confirmar senha</label>
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-2xl border px-4 py-3"
                    style="background: {{ $theme['bg'] }}; border-color: {{ $theme['border'] }}; color: {{ $theme['text'] }}"
                >
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button
                class="text-white px-6 py-3 rounded-2xl font-semibold"
                style="background: {{ $theme['primary'] }}"
            >
                Atualizar senha
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                    style="color: {{ $theme['muted'] }}"
                >
                    Senha atualizada.
                </p>
            @endif
        </div>
    </form>
</section>
