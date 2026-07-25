<!-- resources/views/components/footer.blade.php -->
<footer class="bg-white border-t border-[#E5E7EB] mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <!-- Copyright -->
            <p class="text-xs text-[#6B7280]">
                &copy; {{ date('Y') }} <span class="font-medium text-[#111827]">Alendi</span>. All rights reserved.
            </p>

            <!-- WhatsApp Contact -->
            <div class="flex items-center gap-1 text-xs text-[#6B7280]">
                <span>Need help?</span>
                <a href="https://wa.me/265990705194?text=Hi%2C%20I%20need%20help%20with%20Alendi" 
                   target="_blank"
                   rel="noopener noreferrer"
                   class="font-medium text-[#0F172A] hover:text-[#C80B6D] transition hover:underline">
                   Click me
                </a>
            </div>
        </div>
    </div>
</footer>