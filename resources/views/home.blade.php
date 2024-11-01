
<html>
<head>
    <title>CircleHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
    <!-- Hero Section -->
    <div class="bg-blue-600 text-white rounded-lg shadow-lg mb-8 p-6 px-2 w-full max-w-3xl">
        <h1 class="text-4xl font-bold sm:text-5xl text-center">Welcome to CircleHub</h1>
        <p class="text-lg sm:text-xl mt-2 text-center">Connect with friends and share your moments.</p>
    </div>

    <!-- Login and Register Options -->
    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-lg">
        <h2 class="text-2xl font-semibold mb-4 text-center text-gray-800">Sign In or Sign Up</h2>

        <!-- Login Form -->
        <div class="mb-6">
            <h3 class="text-xl font-medium mb-4 text-gray-800">Login</h3>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="login-email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="login-password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input id="login-password" type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="form-checkbox text-blue-500" />
                        <span class="ml-2 text-gray-700 text-sm">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-blue-500 text-sm hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-black py-2 rounded-md shadow-sm hover:bg-blue-600 transition duration-300">Login</button>
            </form>
        </div>

        <!-- Registration Link -->
        <div class="text-center mt-6">
            <p class="text-gray-600 text-sm mb-2">Don't have an account?</p>
            <a href="{{ route('register') }}" class="text-blue-500 hover:underline text-sm">Sign up</a>
        </div>
    </div>
</div>

</body>
</html>
