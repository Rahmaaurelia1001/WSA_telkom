<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Telkom WSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-red-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Telkom WSA</h1>
            <div class="flex items-center space-x-4">
                <span>{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-gray-200">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 p-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <!-- Upload Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Upload File</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <form action="{{ route('upload.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block mb-2">All Ticket List:</label>
                            <input type="file" name="all_ticket" class="border p-2 rounded" required>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-2">Close Ticket List:</label>
                            <input type="file" name="close_ticket" class="border p-2 rounded" required>
                        </div>
                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                            Process Data
                        </button>
                    </form>
                </div>
            </div>

            <!-- Process Status -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Process Status</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span>Last Process:</span>
                        <span id="processDate">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Status:</span>
                        <span id="processStatus" class="text-green-600">-</span>
                    </div>
                </div>
            </div>

            <!-- Download Section -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Download Results</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span>Processed Data</span>
                        <button id="downloadBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" disabled>
                            Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>