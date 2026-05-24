<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-3xl font-bold mb-6">Criar minha loja</h1>

        <form method="POST" action="{{ route('store.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1">Nome da loja</label>
                <input type="text" name="name" class="w-full border rounded-lg p-3" required>
            </div>

            <div>
                <label class="block mb-1">WhatsApp</label>
                <input type="text" name="whatsapp" class="w-full border rounded-lg p-3">
            </div>

            <div>
                <label class="block mb-1">Descrição</label>
                <textarea name="description" class="w-full border rounded-lg p-3"></textarea>
            </div>

            <button class="bg-purple-600 text-white px-6 py-3 rounded-lg">
                Criar Loja
            </button>
        </form>
    </div>
</x-app-layout>
