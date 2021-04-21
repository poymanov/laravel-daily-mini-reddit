<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-start">
                <div class="w-5/6 overflow-hidden shadow-sm sm:rounded-lg col-span-2 mr-6">
                    @canany(['update', 'delete'], $post)
                        <div class="p-6 bg-white mb-5 sm:rounded-lg">
                            <div class="flex">
                                @can('update', $post)
                                    <a href="{{ route('communities.posts.edit', [$post->community, $post]) }}" class="px-2 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2">Edit post</a>
                                @endcan
                                @can('delete', $post)
                                    <form action="{{ route('communities.posts.destroy', [$post->community, $post]) }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button onclick="return confirm('Are you sure?')" class="px-2 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Delete post</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endcan

                    <div class="p-6 bg-white">
                        <h1 class="text-4xl mb-3 break-all">{{ $post->title }}</h1>

                        @if($post->url)
                            <p class="mb-2">
                                <a class="text-sm text-gray-700 underline" href="{{ $post->url }}" target="_blank">{{ $post->url }}</a>
                            </p>
                        @endif

                        @if($post->largeImageUrl)
                            <img src="{{ $post->largeImageUrl }}" class="mb-2"/>
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

