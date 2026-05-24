<x-app-layout>
    <div class="max-w-3xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-6">Editar categoria</h1>

        <form method="POST" action="{{ route('categories.update', $category) }}" class="bg-white p-6 rounded-xl shadow space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-medium">Nome da categoria</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    class="w-full border rounded-lg p-3"
                    required
                >
            </div>

            <div class="flex gap-3">
                <button class="bg-purple-600 text-white px-6 py-3 rounded-lg">
                    Salvar
                </button>

                <a href="{{ route('categories.index') }}" class="border px-6 py-3 rounded-lg">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
