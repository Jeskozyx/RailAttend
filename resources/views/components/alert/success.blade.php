@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 z-50 max-w-md w-full">
    
    <div class="bg-white rounded-2xl shadow-2xl border-l-4 border-green-500 overflow-hidden">
        <div class="p-5">
            <div class="flex items-start">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Berhasil!</h3>
                    <p class="text-sm text-gray-600">{{ session('success') }}</p>
                </div>
                
                <!-- Close Button -->
                <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="h-1 bg-gray-100">
            <div class="h-full bg-gradient-to-r from-green-400 to-green-600 animate-progress"></div>
        </div>
    </div>
</div>

<style>
    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
    .animate-progress {
        animation: progress 5s linear;
    }
</style>
@endif