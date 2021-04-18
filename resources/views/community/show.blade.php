<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-start">
                <div class="w-5/6 overflow-hidden col-span-2 mr-6">

                    @forelse($posts as $post)
                        <div class="p-6 bg-white mb-5 sm:rounded-lg">
                            <h2 class="text-xl break-all @if($post->text) mb-5 @endif">
                                <a href="{{ route('communities.posts.show', [$community, $post]) }}">{{ $post->title }}</a>
                            </h2>

                            @if($post->text)
                                {{ Str::limit($post->text, $communityPostsTextPreviewLimit) }}
                            @endif
                        </div>
                    @empty
                        <div class="p-6 bg-white mb-5 sm:rounded-lg">
                            No posts yet.
                        </div>
                    @endforelse

                    {{ $posts->links() }}
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
