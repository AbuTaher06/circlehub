<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CircleHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-100">
    <header class="fixed top-0 left-0 w-full bg-blue-900 text-white p-4 shadow-md">
        <div class="container mx-auto flex flex-wrap items-center justify-between">
            <!-- Logo and Search Bar -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold hidden md:block">CircleHub</a>
                <form action="{{ route('profile.search') }}" method="GET" class="flex items-center space-x-2">
                    <input
                        type="text"
                        name="query"
                        placeholder="Search"
                        class="px-4 py-2 rounded-full bg-gray-800 text-white placeholder-gray-400 hidden md:block w-64"
                    />
                    <button type="submit" class="hidden md:block">
                        <i class="fas fa-search text-white"></i>
                    </button>
                </form>
            </div>
            <!-- Navigation Links -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-300 flex items-center space-x-2">
                    <i class="fas fa-home"></i>
                </a>
                <a href="{{ route('messages.all') }}" class="hover:text-gray-300 flex items-center space-x-3">
                    <i class="fas fa-envelope"></i>
                </a>
                <a href="{{ route('notifications') }}" class="hover:text-gray-300 flex items-center space-x-3">
                    <i class="fas fa-bell"></i>
                    @if (Auth::user()->unreadNotifications->count() > 0)
                        <span class="text-red-500">{{ Auth::user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.show', Auth::user()) }}" class="hover:text-gray-300 flex items-center space-x-3">
                    <i class="fas fa-user"></i>
                </a>
                <a href="{{ route('logout') }}" class="hover:text-gray-300 flex items-center space-x-3">
                    <i class="fas fa-sign-out-alt"></i>
                </a>

                <form action="{{ route('user.dark-mode') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="text-white hover:text-gray-300 hidden md:block">
                        Dark Mode
                    </button>
                </form>

                <a href="#" onclick="event.preventDefault(); document.getElementById('dark-mode-form').submit();" class="text-white hover:text-gray-300 hidden md:block">
                    Dark Mode
                </a>
                <form id="dark-mode-form" action="{{ route('user.dark-mode') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </div>
        </div>
    </header>

    <div class="md:hidden fixed top-0 left-0 right-0 bg-blue-900 p-4 flex flex-col items-center">
        <a href="#" class="text-2xl font-bold mb-1">CircleHub</a>
        <form action="{{ route('profile.search') }}" method="GET" class="w-full">
            <div class="flex items-center space-x-2">
                <input
                    type="text"
                    name="query"
                    placeholder="Search"
                    class="w-full px-4 py-2 rounded-full bg-gray-800 text-white placeholder-gray-400 mb-0"
                />
                <button type="submit">
                    <i class="fas fa-search text-white"></i>
                </button>
            </div>
        </form>
        <div class="flex space-x-4 mt-4">
            <a href="{{ route('dashboard') }}" class="text-white text-xl">
                <i class="fas fa-home"></i>
            </a>
            <a href="{{ route('messages.all') }}" class="text-white text-xl">
                <i class="fas fa-envelope"></i>
            </a>
            <a href="{{ route('notifications') }}" class="hover:text-white xl flex items-center space-x-3">
                <i class="fas fa-bell"></i>
                @if (Auth::user()->unreadNotifications->count() > 0)
                    <span class="text-red-500">{{ Auth::user()->unreadNotifications->count() }}</span>
                @endif
            </a>
            <a href="{{ route('profile.show', Auth::user()) }}" class="hover:text-gray-300 flex items-center space-x-3">
                <i class="fas fa-user"></i>
            </a>
            <a href="{{ route('logout') }}" class="text-white text-xl">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="min-h-screen flex items-center justify-center">
        @yield('content')
    </div>

    <script src="{{ asset('js/theme-toggle.js') }}"></script>
    <script>
    function toggleDarkMode() {
        fetch('{{ route('user.dark-mode') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
        }).then(response => {
            if (response.ok) {
                // Optionally handle success
            } else {
                // Handle the error
            }
        });
    }
</script>
</body>
</html>
