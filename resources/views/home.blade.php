<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-start">
                <div class="w-5/6 overflow-hidden col-span-2 mr-6">
                    <x-posts :posts="$posts" :postsTextPreviewLimit="$homePostsTextPreviewLimit"/>
                </div>

                <div class="w-3/12 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <h3 class="text-2xl mb-2 break-all">Communities</h3>

                        @if(count($communities) > 0)
                            <div class="mb-5">
                                @foreach($communities as $community)
                                    <div class="mb-2">
                                        <a href="{{ route('community.show', $community) }}" class="break-words underline">{{ $community->name }}</a>
                                    </div>
                                    <hr class="mb-2">
                                @endforeach
                            </div>

                            <p class="text-center">
                                <a href="{{ route('community.index') }}" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">See all</a>
                            </p>
                        @else
                            <p>No communities yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
