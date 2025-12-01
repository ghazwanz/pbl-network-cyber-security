<div id="modalDetailGaleri" aria-hidden="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex justify-center items-center opacity-0 pointer-events-none transition-all duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-3xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <div class="p-5 pb-3 flex justify-between items-center border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h1 class="text-lg text-[#1B2D62] font-semibold flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-images text-white text-sm"></i>
                </div>
                <span>Detail Galeri</span>
            </h1>
            <button type="button" data-dismiss="modal" class="inline-grid place-items-center text-gray-500 hover:bg-gray-100 rounded-lg min-w-[40px] min-h-[40px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                    <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-6">
            <div class="flex flex-col gap-6">
                <div class="w-full">
                    <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-gradient-to-br from-gray-100 to-gray-200">
                        <img id="detail-gambar" src="" alt="Detail Gambar" class="w-full max-h-[400px] object-cover">
                    </div>
                </div>

                <div class="w-full space-y-5">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Judul Kegiatan</label>
                        <h4 id="detail-judul" class="text-2xl font-semibold text-[#1B2D62] mt-1"></h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                            <label class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-tag"></i> Kategori
                            </label>
                            <div id="detail-tipe" class="mt-2"></div>
                        </div>
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                            <label class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-calendar-alt"></i> Tanggal
                            </label>
                            <div id="detail-tanggal" class="mt-2 text-sm font-semibold text-gray-800"></div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                            <label class="text-xs font-bold text-green-600 uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-map-marker-alt"></i> Lokasi
                            </label>
                            <div id="detail-lokasi" class="mt-2 text-sm font-semibold text-gray-800"></div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <p id="detail-deskripsi" class="text-gray-700 text-sm mt-2 leading-relaxed break-words"></p>
                    </div>
                    
                    <div id="detail-featured-container" class="hidden">
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white font-semibold rounded-lg shadow-md">
                            <i class="fas fa-star"></i>
                            <span>Konten Unggulan</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 pt-3 flex justify-end gap-3 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-2xl">
            <button type="button" data-dismiss="modal" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>