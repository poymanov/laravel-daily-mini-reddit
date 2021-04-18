<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-start">
                <div class="w-5/6 bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2 mr-6">
                    <div class="p-6 bg-white">
                        <h1 class="text-4xl mb-3 break-all">{{ $post->title }}</h1>

                        @if($post->url)
                            <p class="mb-2">
                                <a class="text-sm text-gray-700 underline" href="{{ $post->url }}" target="_blank">{{ $post->url }}</a>
                            </p>
                        @endif

                        @if($post->text)
                            <p class="mb-2">
                                {{ $post->text }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="w-3/12 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <h3 class="text-2xl mb-2 break-all">{{ $community->name }}</h3>
                        <p>{{ $community->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

