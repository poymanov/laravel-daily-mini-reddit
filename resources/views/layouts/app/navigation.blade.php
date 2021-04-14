<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}">Mini Reddit</a>
                </div>
            </div>

            @if (Route::has('login'))
                <div class="py-4">
                    @auth
                        <a href="{{ url('/profile') }}" class="text-sm text-gray-700 underline mr-4">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf

                            <a href="{{ route('logout') }}" class="text-sm text-gray-700 underline" onclick="event.preventDefault();
                                            this.closest('form').submit();">Log out</a>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 underline mr-4">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm text-gray-700 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

        </div>
    </div>
</nav>
