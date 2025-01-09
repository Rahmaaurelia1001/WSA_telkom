<!DOCTYPE html>
<html>
<head>
    <title>Login - Telkom WSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white h-screen w-screen flex items-center justify-center">
    <div class="p-8 rounded-lg w-96 mt-[-100px]"> <!-- Gunakan Tailwind kelas negatif margin -->
        <div class="flex justify-center mb-12">
            <img src="/images/logo-telkom.png" alt="Logo Telkom" class="h-35 w-auto mx-auto">
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
        <div class= >
            <div class="mb-4">
                <input type="text" 
                       name="email" 
                       placeholder="Email"
                       class="w-full p-2 border rounded"
                       required>
            </div>

            <div class="mb-6">
                <input type="password" 
                       name="password" 
                       placeholder="Password"
                       class="w-full p-2 border rounded"
                       required>
            </div>

            <button type="submit" 
                    class="w-full bg-red-600 text-white p-2 rounded hover:bg-red-700">
                Login
            </button>
        </div>
        </form>
    </div>
</div>

    </div>
</body>
</html>
