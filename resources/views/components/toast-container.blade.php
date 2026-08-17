@php
    $flashToasts = [];

    if (session()->has('success')) {
        $flashToasts[] = ['type' => 'success', 'message' => (string) session('success')];
    }

    if (session()->has('error')) {
        $flashToasts[] = ['type' => 'error', 'message' => (string) session('error')];
    }

    if (session()->has('warning')) {
        $flashToasts[] = ['type' => 'warning', 'message' => (string) session('warning')];
    }

    if (session()->has('info')) {
        $flashToasts[] = ['type' => 'info', 'message' => (string) session('info')];
    }

    if (session()->has('status')) {
        $flashToasts[] = ['type' => 'success', 'message' => (string) session('status')];
    }

    if ($errors->any() && ! session()->has('error')) {
        $flashToasts[] = [
            'type' => 'error',
            'message' => count($errors->all()) === 1
                ? (string) $errors->first()
                : 'Please check the required fields',
        ];
    }
@endphp

<div
    x-data="toastRoot({{ \Illuminate\Support\Js::from($flashToasts) }})"
    x-cloak
    class="pointer-events-none fixed top-3 right-3 z-[90] flex w-[min(22rem,calc(100%-1.5rem))] flex-col items-stretch gap-3 sm:top-4 sm:right-4"
    role="region"
    aria-label="Notifications"
    aria-live="polite"
    aria-relevant="additions"
>
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div
            class="pointer-events-auto w-full overflow-hidden rounded-xl border border-[#E5E7EB] bg-white shadow-[0_10px_30px_rgba(17,24,39,0.12)]"
            :class="toast.leaving ? 'toast-leave' : 'toast-enter'"
            role="status"
            aria-atomic="true"
            :aria-live="toast.type === 'error' ? 'assertive' : 'polite'"
            @mouseenter="pause(toast.id)"
            @mouseleave="resume(toast.id)"
            @focusin="pause(toast.id)"
            @focusout="resume(toast.id)"
        >
            <div class="flex items-start gap-3 px-4 py-3">
                <div class="toast-icon-pop mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full"
                     :class="{
                        'bg-[#ca0251]/10 text-[#ca0251]': toast.type === 'success',
                        'bg-red-50 text-red-600': toast.type === 'error',
                        'bg-amber-50 text-amber-600': toast.type === 'warning',
                        'bg-slate-100 text-slate-600': toast.type === 'info'
                     }">
                    <template x-if="toast.type === 'success'">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path class="toast-check-mark" d="M6.5 12.5l3.5 3.5 7.5-8"
                                  stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                        </svg>
                    </template>
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#6B7280]" x-text="title(toast.type)"></p>
                    <p class="mt-0.5 break-anywhere text-sm font-medium text-[#111827]" x-text="toast.message"></p>
                </div>

                <button
                    type="button"
                    class="rounded-lg p-1 text-[#6B7280] transition hover:bg-[#F3F4F6] hover:text-[#111827]"
                    @click="dismiss(toast.id)"
                    aria-label="Dismiss notification"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
