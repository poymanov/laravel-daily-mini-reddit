<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $community->name }} - Edit Post
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('communities.posts.update', [$community, $post]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <!-- Title -->
                        <div class="mb-4">
                            <x-label for="title" :value="__('Title')" />

                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $post->title)" required autofocus />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-label for="text" :value="__('Text')" />

                            <x-textarea id="text" class="block mt-1 w-full" name="text">{{ old('text', $post->text) }}</x-textarea>
                        </div>

                        <!-- Url -->
                        <div class="mb-4">
                            <x-label for="url" :value="__('Url')" />

                            <x-input id="url" class="block mt-1 w-full" type="text" name="url" :value="old('url', $post->url)"/>
                        </div>

                        <div class="mb-4">
                            <div class="mb-5">
                                <x-label for="image" :value="__('Image')" />
                                <x-input id="image" class="block mt-1 w-full" type="file" accept="image/*" name="image"/>
                            </div>

                            <div>
                                @if ($post->largeImageUrl)
                                    <img src="{{ $post->largeImageUrl }}" class="mb-4">

                                    <div class="mb-4">
                                        <x-label for="delete-image">
                                            <x-input id="delete-image" type="checkbox" name="delete_image" />
                                            {{ __('Delete image') }}
                                        </x-label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Update') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

