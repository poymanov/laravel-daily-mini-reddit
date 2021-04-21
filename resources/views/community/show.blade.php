<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-start">
                <div class="w-5/6 overflow-hidden col-span-2 mr-6">
                    <x-posts :posts="$posts" :postsTextPreviewLimit="$communityPostsTextPreviewLimit"/>
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
