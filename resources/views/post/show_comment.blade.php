<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white mb-5 sm:rounded-lg">
                    <a class="p-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2" href="{{ route('communities.posts.show', [$community, $post]) }}">{{ $post->title }}</a>
                </div>
                @can('delete', $comment)
                    <div class="p-6 bg-white mb-5 sm:rounded-lg">
                        <form
                            action="{{ route('communities.posts.comments.destroy', [$community, $post, $comment]) }}"
                            method="post">
                            @csrf
                            @method('delete')
                            <button
                                class="p-1 px-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure?')">Delete Comment
                            </button>
                        </form>
                    </div>
                @endcan

                <div class="p-6 bg-white sm:rounded-lg mb-2">
                    <div class="mb-2">
                        {{ $comment->text }}
                    </div>
                    <div class="text-sm mb-2">
                        Author: {{ $comment->user->name }} Created: {{ $comment->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

