@if($comments->count() > 0)
    <div class="p-6 bg-white sm:rounded-lg mt-2">
        <h2 class="text-2xl mb-2">Comments: {{ $comments->count() }}</h2>
        <hr class="mb-3">
        @foreach($comments as $comment)
            <div class="py-3">
                <div class="mb-2">
                    {{ $comment->text }}
                </div>
                <div class="text-sm mb-2">
                    Author: {{ $comment->user->name }} Created: {{ $comment->created_at->diffForHumans() }}
                </div>
                @canany(['update'], $comment)
                    <div class="text-xs mt-3">
                        @can('update', $comment)
                            <a href="{{ route('communities.posts.comments.edit', [$community, $post, $comment]) }}" class="p-1 px-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Edit Comment</a>
                        @endcan
                    </div>
                @endcan
            </div>
            <hr class="my-2">
        @endforeach
    </div>
@endif
