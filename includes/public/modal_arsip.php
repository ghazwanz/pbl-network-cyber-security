<div id="modalDetailArsip" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex justify-center items-center opacity-0 pointer-events-none transition-all duration-300 ease-out z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-4xl scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto mx-4">
        
        <div class="p-5 pb-3 flex justify-between items-center border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-2xl">
            <h1 class="text-lg text-[#1B2D62] font-semibold flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-pdf text-white text-sm"></i>
                </div>
                <span>Detail Arsip</span>
            </h1>
            <button type="button" data-dismiss="modal" class="inline-grid place-items-center text-gray-500 hover:bg-gray-100 rounded-lg min-w-[40px] min-h-[40px] transition-all">
                <svg width="1.5em" height="1.5em" stroke-width="1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                    <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-6">
            <div class="flex flex-col gap-6">
                
                <div class="flex items-start gap-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-file-pdf text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span id="detail-kategori-arsip" class="px-3 py-1.5 text-xs font-bold rounded-lg uppercase tracking-wide"></span>
                            <span id="detail-featured-badge-arsip" class="hidden px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-xs font-bold rounded-lg">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        </div>
                        <h2 id="detail-judul-arsip" class="text-2xl font-bold text-[#1B2D62] leading-tight"></h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                        <label class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-calendar-alt"></i> Tahun Publikasi
                        </label>
                        <div id="detail-tahun-arsip" class="mt-2 text-lg font-bold text-gray-800"></div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                        <label class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-building"></i> Penerbit
                        </label>
                        <div id="detail-penerbit-arsip" class="mt-2 text-sm font-semibold text-gray-800 line-clamp-2"></div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl border border-green-200">
                        <label class="text-xs font-bold text-green-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-download"></i> Total Unduhan
                        </label>
                        <div id="detail-download-arsip" class="mt-2 text-lg font-bold text-gray-800"></div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200">
                        <label class="text-xs font-bold text-purple-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-file"></i> Ukuran File
                        </label>
                        <div id="detail-filesize-arsip" class="mt-2 text-lg font-bold text-gray-800"></div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1 mb-3">
                        <i class="fas fa-users"></i> Penulis / Kontributor
                    </label>
                    <div id="detail-penulis-list-arsip" class="space-y-2"></div>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                        <i class="fas fa-align-left"></i> Abstrak
                    </label>
                    <p id="detail-abstrak-arsip" class="text-gray-700 text-sm mt-2 leading-relaxed"></p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-200">
                        <label class="text-xs font-bold text-orange-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-tags"></i> Kata Kunci
                        </label>
                        <div id="detail-keywords-arsip" class="mt-2 flex flex-wrap gap-2"></div>
                    </div>
                    <div id="detail-doi-container-arsip" class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                        <label class="text-xs font-bold text-blue-600 uppercase tracking-wide flex items-center gap-1">
                            <i class="fas fa-link"></i> DOI
                        </label>
                        <a id="detail-doi-arsip" href="#" target="_blank" class="mt-2 text-sm font-semibold text-blue-700 hover:underline block break-all"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 pt-3 flex flex-col sm:flex-row justify-end gap-3 border-t border-gray-200 sticky bottom-0 bg-white rounded-b-2xl">
            <button type="button" data-dismiss="modal" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium border-2 border-gray-300 text-gray-700 hover:bg-gray-100 transition-all duration-300">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
            <a id="detail-download-btn-arsip" target="_blank" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-medium bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:shadow-lg hover:scale-105 transition-all duration-300">
                <i class="fas fa-file-pdf mr-2"></i>Lihat PDF
            </a>
        </div>
    </div>
</div>