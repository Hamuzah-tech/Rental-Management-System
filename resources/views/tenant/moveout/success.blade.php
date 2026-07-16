<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Submitted</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="max-w-xl mx-auto p-4 w-full">

        <div class="bg-white rounded-xl shadow-lg p-8 text-center">

            <div class="text-6xl mb-4">
                ✅
            </div>

            <h1 class="text-2xl font-bold text-green-700">
                Notice Submitted Successfully
            </h1>

            <p class="mt-4 text-gray-600">
                Your move-out notice has been received.
                Your landlord will review it shortly.
            </p>

            <p class="mt-6 text-sm text-gray-500">
                This window will close automatically in <span id="countdown">5</span> seconds...
            </p>

            <button onclick="closeWindow()" 
                    class="mt-4 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Close Now
            </button>

        </div>

    </div>

    <script>
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(function() {
            seconds--;
            countdownEl.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                closeWindow();
            }
        }, 1000);

        function closeWindow() {
            window.close();
            
            // Fallback: if window.close() is blocked, go back
            setTimeout(function() {
                window.history.back();
            }, 500);
        }
    </script>

</body>
</html>