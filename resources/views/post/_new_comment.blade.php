@auth
    <div class="p-6 bg-white sm:rounded-lg">
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <h2 class="text-2xl mb-3">New Comment</h2>
        <form method="POST" action="{{ route('communities.posts.comments.store', [$community, $post]) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <x-label for="text" :value="__('Text')" />

                <x-textarea id="text" class="block mt-1 w-full" name="text">{{ old('text') }}</x-textarea>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Post Comment') }}
                </x-button>
            </div>
        </form>
    </div>
@endauth
