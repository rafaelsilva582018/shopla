@php
    $theme = auth()->user()?->store?->dashboardTheme() ?? config('dashboard-themes.blush');
    $inputStyle = 'background: ' . $theme['bg'] . '; border-color: ' . $theme['border'] . '; color: ' . $theme['text'];
@endphp

<section>
    <header class="flex items-start gap-4">
        <span class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: {{ $theme['secondary'] }}; color: {{ $theme['primary'] }}">
            <x-dashboard-icon name="store" class="w-6 h-6" />
        </span>

        <div>
            <h2 class="text-xl font-bold">
                Dados do perfil
            </h2>

            <p class="mt-1 text-sm" style="color: {{ $theme['muted'] }}">
                Informações da pessoa responsável pela loja e localização usada no painel.
            </p>
        </div>
    </header>

    <div class="mt-6 rounded-3xl border p-4 flex items-start gap-3" style="background: {{ $theme['secondary'] }}; border-color: {{ $theme['border'] }}">
        <span class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0" style="background: {{ $theme['card'] }}; color: {{ $theme['primary'] }}">
            <x-dashboard-icon name="wallet" class="w-5 h-5" />
        </span>
        <div>
            <p class="font-bold">Dados para assinatura</p>
            <p class="text-sm mt-1" style="color: {{ $theme['muted'] }}">
                Para assinar um plano pelo Asaas, preencha CPF/CNPJ, CEP, rua, numero e bairro.
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('patch')

        <div>
            <h3 class="font-bold mb-4">Identificação</h3>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-semibold mb-2">Nome</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="given-name" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-semibold mb-2">Sobrenome</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <div>
                    <label for="birthdate" class="block text-sm font-semibold mb-2">Data de nascimento</label>
                    <input id="birthdate" name="birthdate" type="date" value="{{ old('birthdate', optional($user->birthdate)->format('Y-m-d')) }}" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <p class="text-xs mt-2" style="color: {{ $theme['muted'] }}">Opcional, útil para lembretes e atendimento.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('birthdate')" />
                </div>

                <div>
                    <label for="document" class="block text-sm font-semibold mb-2">CPF/CNPJ</label>
                    <input id="document" name="document" type="text" value="{{ old('document', $user->document) }}" placeholder="Obrigatorio para assinar" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('document')" />
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-bold mb-4">Contato</h3>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-semibold mb-2">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold mb-2">Celular / WhatsApp</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="(00) 00000-0000" autocomplete="tel" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-bold mb-4">Localização</h3>

            <div class="grid md:grid-cols-[1fr_120px] gap-5">
                <div>
                    <label for="city" class="block text-sm font-semibold mb-2">Cidade</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $user->city) }}" placeholder="Ex: Guararapes" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>

                <div>
                    <label for="state" class="block text-sm font-semibold mb-2">UF</label>
                    <input id="state" name="state" type="text" value="{{ old('state', $user->state) }}" placeholder="SP" maxlength="2" class="w-full rounded-2xl border px-4 py-3 uppercase" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('state')" />
                </div>
            </div>

            <p class="text-xs mt-2" style="color: {{ $theme['muted'] }}">
                Cidade e UF alimentam o clima e o relógio da tela inicial.
            </p>
        </div>

        <div>
            <h3 class="font-bold mb-4">Endereço</h3>

            <div class="grid md:grid-cols-3 gap-5">
                <div>
                    <label for="zip_code" class="block text-sm font-semibold mb-2">CEP</label>
                    <input id="zip_code" name="zip_code" type="text" value="{{ old('zip_code', $user->zip_code) }}" placeholder="00000-000" autocomplete="postal-code" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('zip_code')" />
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold mb-2">Rua / Logradouro</label>
                    <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}" autocomplete="street-address" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>

                <div>
                    <label for="address_number" class="block text-sm font-semibold mb-2">Número</label>
                    <input id="address_number" name="address_number" type="text" value="{{ old('address_number', $user->address_number) }}" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('address_number')" />
                </div>

                <div>
                    <label for="district" class="block text-sm font-semibold mb-2">Bairro</label>
                    <input id="district" name="district" type="text" value="{{ old('district', $user->district) }}" class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('district')" />
                </div>

                <div>
                    <label for="address_complement" class="block text-sm font-semibold mb-2">Complemento</label>
                    <input id="address_complement" name="address_complement" type="text" value="{{ old('address_complement', $user->address_complement) }}" placeholder="Casa, apto, bloco..." class="w-full rounded-2xl border px-4 py-3" style="{{ $inputStyle }}">
                    <x-input-error class="mt-2" :messages="$errors->get('address_complement')" />
                </div>
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-2xl border p-4" style="border-color: {{ $theme['border'] }}">
                <p class="text-sm" style="color: {{ $theme['muted'] }}">
                    Seu e-mail ainda não foi verificado.

                    <button form="send-verification" class="font-semibold underline" style="color: {{ $theme['primary'] }}">
                        Reenviar verificação
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        Um novo link de verificação foi enviado.
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <button class="text-white px-6 py-3 rounded-2xl font-semibold" style="background: {{ $theme['primary'] }}">
                Salvar alterações
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm"
                    style="color: {{ $theme['muted'] }}"
                >
                    Dados salvos.
                </p>
            @endif
        </div>
    </form>
</section>
