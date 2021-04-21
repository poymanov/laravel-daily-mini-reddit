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
        </div>
    @empty
        <div class="p-6 bg-white mb-5 sm:rounded-lg">
            No posts yet.
        </div>
    @endforelse

    {{ $posts->links() }}
</div>
