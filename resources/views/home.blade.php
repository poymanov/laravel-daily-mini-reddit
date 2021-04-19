<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-start">
                <div class="w-5/6 overflow-hidden col-span-2 mr-6">

                    @forelse($posts as $post)
                        <div class="p-6 bg-white mb-5 sm:rounded-lg">
                            <h2 class="text-xl break-all @if($post->text) mb-5 @endif">
                                <a href="{{ route('communities.posts.show', [$post->community, $post]) }}">{{ $post->title }}</a>
                            </h2>

                            @if($post->text)
                                {{ Str::limit($post->text, $homePostsTextPreviewLimit) }}
                            @endif
                        </div>
                    @empty
                        <div class="p-6 bg-white mb-5 sm:rounded-lg">
                            No posts yet.
                        </div>
                    @endforelse

                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
