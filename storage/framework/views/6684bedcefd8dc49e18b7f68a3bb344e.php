<div
    x-data="{ 
        notifications: [],
        add(e) {
            this.notifications.push({
                id: e.timeStamp,
                type: e.detail.type,
                message: e.detail.message,
            });
            setTimeout(() => {
                this.remove(e.timeStamp)
            }, 3000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    @notify.window="add($event)"
    class="fixed bottom-[280px] md:bottom-[220px] right-4 sm:right-8 z-[80] flex flex-col gap-3 pointer-events-none"
>
    <template x-for="notification in notifications" :key="notification.id">
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="max-w-sm w-full bg-[#111] text-white shadow-[0_8px_30px_rgb(0,0,0,0.2)] rounded-2xl pointer-events-auto border border-white/10 overflow-hidden flex items-center p-3.5 gap-3"
        >
            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full"
                 :class="notification.type === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'">
                <template x-if="notification.type === 'success'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <template x-if="notification.type === 'error'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
            </div>
            <div class="flex-1">
                <p x-text="notification.message" class="text-sm font-semibold tracking-wide"></p>
            </div>
            <button @click="remove(notification.id)" class="text-gray-400 hover:text-white transition-colors p-1 rounded-full hover:bg-white/10">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
    </template>
</div>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views/components/frontend/toast-notification.blade.php ENDPATH**/ ?>