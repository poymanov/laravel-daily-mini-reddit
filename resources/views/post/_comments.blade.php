@if($comments->count() > 0)
    <div class="p-6 bg-white sm:rounded-lg mt-2">
    <h2 class="text-2xl mb-2">Comments: {{ $comments->count() }}</h2>
    <hr class="mb-3">
    @foreach($comments as $comment)
        <div class="py-3">
            <div class="mb-2">
                {{ $comment->text }}
            </div>
            <div class="text-sm">
                Author: {{ $comment->user->name }} Created: {{ $comment->created_at->diffForHumans() }}
            </div>
        </div>
        <hr class="my-2">
    @endforeach
    </div>
@endif
