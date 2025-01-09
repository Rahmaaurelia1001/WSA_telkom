<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assurance Customer Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100" style = "overflow: hidden!important;height: 100%!important;">
    <!-- Header -->
    <div class="bg-white shadow-md flex justify-between items-center px-6 py-3">
        <img src="/images/logo-telkom.png" alt="Telkom Indonesia" class="h-10">
        <div class="flex items-center space-x-2">
            <div class="bg-gray-300 rounded-full p-2">
                <!-- Fixed the img tag here -->
                <img src="/images/user.png" alt="Assurance" class="w-6 h-6 text-gray-600">
            </div>
            <span class="text-gray-600 font-medium">XXXXXXXXXX</span>
        </div>
    </div>
    <div class="flex flex-col items-center justify-center h-screen space-y-4">
    <!-- Welcome and Logo Telkom -->
    <div class="flex items-center space-x-10" style = "margin-top : -100px!important;">
        <h1 class="text-3xl font-bold mt-6 mb-6">
            Welcome to <br>
            <span class="text-red-600 text-4xl">Assurance Customer Page</span>
        </h1>
        <!-- Telkom Logo placed next to the welcome text -->
        <img src="/images/logo-telkom.png" alt="Telkom Indonesia" style = "width : 200px!important; margin-top : -45px!important;">
    </div>

    <!-- WSA Icon -->
    <a href="{{ route('upload.form') }}">
            <div class="border-4 border-red-600 rounded-lg p-4 flex items-center justify-center bg-white shadow-md">
                <img src="/images/assurance.png" alt="WSA" class="h-20">
            </div>
        </a>
</div>

</div>
</body>
</html>
