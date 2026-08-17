const queue = [];

function store() {
    return window.Alpine?.store?.('toasts');
}

function flushQueue() {
    const toasts = store();
    if (!toasts) {
        return;
    }

    while (queue.length) {
        toasts.add(queue.shift());
    }
}

window.notify = {
    show(type, message) {
        const text = String(message ?? '').trim();
        if (!text) {
            return;
        }

        const payload = { type, message: text };
        const toasts = store();

        if (toasts) {
            toasts.add(payload);
            return;
        }

        queue.push(payload);
    },
    success(message) {
        this.show('success', message);
    },
    error(message) {
        this.show('error', message);
    },
    warning(message) {
        this.show('warning', message);
    },
    info(message) {
        this.show('info', message);
    },
};

document.addEventListener('alpine:init', () => {
    window.Alpine.store('toasts', {
        items: [],
        add(toast) {
            const type = ['success', 'error', 'warning', 'info'].includes(toast.type)
                ? toast.type
                : 'info';

            const item = {
                id: Date.now() + Math.random(),
                type,
                message: toast.message,
                leaving: false,
                timer: null,
            };

            this.items.push(item);
            item.timer = setTimeout(() => this.dismiss(item.id), this.duration(type));
        },
        duration(type) {
            if (type === 'error') {
                return 7000;
            }
            if (type === 'warning') {
                return 5500;
            }
            return 4200;
        },
        pause(id) {
            const item = this.items.find((toast) => toast.id === id);
            if (!item || item.leaving) {
                return;
            }

            if (item.timer) {
                clearTimeout(item.timer);
                item.timer = null;
            }
        },
        resume(id) {
            const item = this.items.find((toast) => toast.id === id);
            if (!item || item.leaving || item.timer) {
                return;
            }

            item.timer = setTimeout(() => this.dismiss(item.id), this.duration(item.type));
        },
        dismiss(id) {
            const item = this.items.find((toast) => toast.id === id);
            if (!item || item.leaving) {
                return;
            }

            item.leaving = true;
            if (item.timer) {
                clearTimeout(item.timer);
            }

            setTimeout(() => {
                this.items = this.items.filter((toast) => toast.id !== id);
            }, 250);
        },
    });

    window.Alpine.data('toastRoot', (flash = []) => ({
        init() {
            flushQueue();
            (flash || []).forEach((toast) => window.notify.show(toast.type, toast.message));
        },
        dismiss(id) {
            this.$store.toasts.dismiss(id);
        },
        pause(id) {
            this.$store.toasts.pause(id);
        },
        resume(id) {
            this.$store.toasts.resume(id);
        },
        title(type) {
            return {
                success: 'Success',
                error: 'Error',
                warning: 'Warning',
                info: 'Notice',
            }[type] || 'Notice';
        },
    }));
});

document.addEventListener('alpine:initialized', flushQueue);
