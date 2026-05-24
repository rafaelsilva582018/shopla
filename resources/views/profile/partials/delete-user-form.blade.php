@php
    $theme = auth()->user()?->store?->dashboardTheme() ?? config('dashboard-themes.blush');
@endphp

<section class="space-y-5">
    <header class="flex items-start gap-4">
        <span class="w-12 h-12 rounded-2xl flex items-center justify-center bg-red-100 text-red-600">
            <x-dashboard-icon name="cancel" class="w-6 h-6" />
        </span>

        <div>
            <h2 class="text-xl font-bold">
                Zona de cuidado
            </h2>

            <p class="mt-1 text-sm" style="color: {{ $theme['muted'] }}">
                Excluir a conta remove seus dados de forma permanente.
            </p>
        </div>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-3 rounded-2xl font-semibold border text-red-600 bg-red-50 border-red-100"
    >
        Excluir minha conta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4">
                <span class="h-14 w-14 shrink-0 rounded-2xl flex items-center justify-center bg-red-100 text-red-600">
                    <x-dashboard-icon name="cancel" class="w-7 h-7" />
                </span>

                <div>
                    <p class="text-xs font-bold tracking-widest uppercase text-red-500">Confirmacao</p>
                    <h2 class="text-2xl font-black text-gray-900">
                        Excluir conta?
                    </h2>

                    <p class="mt-2 text-sm text-gray-600">
                        Essa acao e permanente. Digite sua senha para confirmar.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <label for="password" class="sr-only">Senha</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full rounded-2xl border-gray-300 px-4 py-3"
                    placeholder="Senha"
                >

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <button class="px-5 py-2 rounded-xl font-semibold bg-red-600 text-white">
                    Excluir conta
                </button>
            </div>
        </form>
    </x-modal>
</section>
