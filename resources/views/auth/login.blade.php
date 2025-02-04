<!DOCTYPE html>
<html>
<head>
    <title>Login - Telkom WSA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white min-h-screen w-full md:h-screen md:w-screen flex items-center justify-center px-4 md:px-0">
            <div class="p-4 md:p-8 rounded-lg w-full max-w-sm md:w-96 mt-0 md:mt-[-100px]">
                <div class="flex justify-center mb-8 md:mb-12">
                    <img src="/images/logo-telkom.png" alt="Logo Telkom" class="h-24 md:h-35 w-auto mx-auto">
                </div>
                @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Login gagal!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div>
                        <div class="mb-4">
                            <input type="text" name="email" placeholder="Email" 
                                class="w-full p-2 md:p-2 border rounded text-sm md:text-base" required>
                        </div>
                        <div class="mb-6">
                            <input type="password" name="password" placeholder="Password" 
                                class="w-full p-2 md:p-2 border rounded text-sm md:text-base" required>
                        </div>
                        <button type="submit" 
                            class="w-full bg-red-600 text-white p-2 rounded hover:bg-red-700 text-sm md:text-base">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>