<div>
    @forelse($posts as $post)
        <div class="p-6 bg-white mb-5 sm:rounded-lg">
            <h2 class="text-xl break-all @if($post->text) mb-5 @endif">
                <a href="{{ route('communities.posts.show', [$post->community, $post]) }}">{{ $post->title }}</a>
            </h2>

            @if($post->largeImageUrl)
                <img src="{{ $post->largeImageUrl }}" class="mb-2"/>
            @endif

            @if($post->text)
                {{ Str::limit($post->text, $postsTextPreviewLimit) }}
            @endif

            @can('vote', $post)
                <div class="flex mt-2">
                    @if(!$post->current_user_like)
                        <form action="{{ route('communities.posts.votes.store', [$post->community, $post]) }}" method="post" class="mr-2">
                            @csrf
                            <input type="hidden" name="vote" value="1">
                            <button class="px-2 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Like post</button>
                        </form>
                    @endif
                    @if(!$post->current_user_dislike)
                        <form action="{{ route('communities.posts.votes.store', [$post->community, $post]) }}" method="post">
                            @csrf
                            <input type="hidden" name="vote" value="-1">
                            <button class="px-2 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Dislike post</button>
                        </form>
                    @endif
                </div>

            @endcan
        </div>
    @empty
        <div class="p-6 bg-white mb-5 sm:rounded-lg">
            No posts yet.
        </div>
    @endforelse

    {{ $posts->links() }}
</div>
