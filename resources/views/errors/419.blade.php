<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Session Expired</h1>
        <p class="text-gray-600 mb-6">
            Your session has expired. Please refresh the page to continue.
        </p>

        <!-- Reasons -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left text-sm text-gray-600 space-y-2">
            <p class="font-medium text-gray-700 mb-2">This usually happens when:</p>
            <div class="flex items-start gap-2">
                <span class="text-gray-400">•</span>
                <span>You left the page idle for too long</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="text-gray-400">•</span>
                <span>You submitted a form after the session expired</span>
            </div>
            <div class="flex items-start gap-2">
                <span class="text-gray-400">•</span>
                <span>Your browser cookies were cleared</span>
            </div>
        </div>

        <!-- Home Button -->
        <a href="{{ route('home') }}" 
           class="block w-full bg-[#ca0251] hover:bg-[#a80244] text-white font-semibold py-3 px-4 rounded-lg transition">
            Home
        </a>
    </div>

    <!-- Auto-refresh after 10 seconds -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 10000);
    </script>
</body>
</html>