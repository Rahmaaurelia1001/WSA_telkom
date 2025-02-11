<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>


<body class="bg-gray-100">
@include('admin/navbar-admin')

    <!-- Container -->
    <div class="container mx-auto p-6">
        <!-- Create New User Form -->
        <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Create New User</h2>

            <form id="createUserForm" method="POST" action="{{ route('admin.users.createUser') }}">
    @csrf
    
    <!-- Name Field -->
    <div class="mb-6">
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:outline-none" required>
    </div>

    <!-- Email Field -->
    <div class="mb-6">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:outline-none" required>
    </div>

    <!-- Password Field -->
    <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:outline-none" required>
    </div>

    <!-- Password Confirmation Field -->
    <div class="mb-6">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input type="password" name="password_confirmation" class="w-full p-3 mt-2 border border-gray-300 rounded-lg focus:outline-none" required>
    </div>

    <!-- Submit Button -->
<button type="submit" class="w-full px-6 py-3 bg-[#D50000] text-white font-semibold rounded-lg">Create User</button>

</form>

        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan form sudah dimuat
        const form = document.getElementById('createUserForm');
        
        // Cek jika form ditemukan
        if (form) {
            form.addEventListener('submit', function(event) {
                // Mengambil nilai dari setiap field input
                const name = document.querySelector('input[name="name"]').value;
                const email = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;
                const passwordConfirmation = document.querySelector('input[name="password_confirmation"]').value;

                // Menampilkan data form di konsol
                console.log('Form Data:');
                console.log('Name:', name);
                console.log('Email:', email);
                console.log('Password:', password);
                console.log('Password Confirmation:', passwordConfirmation);
            });
        } else {
            console.log('Form not found!');
        }
    });
</script>


</body>

</html>
