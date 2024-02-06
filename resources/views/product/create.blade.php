<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto p-6 bg-white border-b border-gray-200">
                    <div class="min-w-full align-middle">
                        <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Add new product
                            </div>
                            <!-- Name -->
                            <div>
                                <x-label for="name" :value="__('Name')" />

                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            </div>

                            <!-- Price -->
                            <div class="mt-4">
                                <x-label for="price" :value="__('Price')" />

                                <x-input id="price" class="block mt-1 w-full" type="text" name="price" :value="old('price')" required />
                            </div>

                            <!-- Photo -->
                            <div class="mt-4">
                                <x-label for="image" :value="__('Photo')" />

                                <input type="file" id="image" class="block mt-1 w-full" name="image" />
                            </div>

                            <div class="flex items-center mt-4">
                                <x-button>
                                    {{ __('Save') }}
                                </x-button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
