{{-- Modal Tambah Rangkaian Gerbong --}}
{{-- Usage: @include('components.modal-rangkaian', ['train' => $train]) --}}

<dialog id="modalRangkaian"
    class="modal bg-transparent p-0 m-auto rounded-none border-0 max-w-xl w-full backdrop:bg-black/60 backdrop:backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mx-4 animate-fade-in">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-[#001D4B] to-[#003D7A] px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Tambah Rangkaian</h3>
                        <p class="text-blue-200 text-xs">Generate gerbong otomatis</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modalRangkaian').close()"
                    class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center text-white/70 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content --}}
        <form action="{{ route('rangkaian.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="train_id" value="{{ $train->id ?? '' }}">

            {{-- Train Info Banner --}}
            <div
                class="bg-gradient-to-r from-orange-50 to-amber-50 p-4 rounded-xl border border-orange-100 mb-6 flex items-center space-x-3">
                <div
                    class="w-12 h-12 bg-[#FF7300] rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-wider">Kereta Terpilih</p>
                    <p class="text-lg font-black text-[#001D4B]">{{ $train->name ?? '-' }}</p>
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="space-y-5">
                {{-- Type & Label --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center">
                            <span class="w-1.5 h-1.5 bg-[#FF7300] rounded-full mr-2"></span>
                            Tipe Gerbong
                        </label>
                        <select name="type"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-[#001D4B] focus:ring-2 focus:ring-[#FF7300] focus:border-transparent transition-all">
                            <option value="EKS">🎫 Eksekutif</option>
                            <option value="EKO">🎟️ Ekonomi</option>
                            <option value="LUX">👑 Luxury</option>
                            <option value="KMP">🍽️ Kereta Makan</option>
                            <option value="BP">⚡ Pembangkit</option>
                        </select>
                    </div>
                </div>

                {{-- Quantity & Start Number --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>
                            Jumlah Gerbong
                        </label>
                        <div class="relative">
                            <input type="number" name="jumlah" value="1" min="1" max="20"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-[#001D4B] text-center focus:ring-2 focus:ring-[#FF7300] focus:border-transparent transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-slate-400">unit</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                            Mulai Dari No.
                        </label>
                        <input type="number" name="start_number" value="1" min="1"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm font-bold text-[#001D4B] text-center focus:ring-2 focus:ring-[#FF7300] focus:border-transparent transition-all">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-3 mt-6 pt-5 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalRangkaian').close()"
                    class="px-5 py-2.5 text-slate-500 hover:text-slate-700 font-semibold text-sm rounded-xl hover:bg-slate-100 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-[#FF7300] to-[#FF9500] hover:from-[#e66800] hover:to-[#e68800] text-white font-bold text-sm rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-xl transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>GENERATE</span>
                </button>
            </div>
        </form>
    </div>
</dialog>

<style>
    dialog::backdrop {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }
</style>
