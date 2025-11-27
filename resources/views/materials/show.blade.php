<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Pembelajaran — CodePlay</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-white text-gray-800">

    {{-- HEADER NAVIGASI --}}
    <div class="border-b border-gray-200 sticky top-0 bg-white z-10">
        <div class="container mx-auto px-6 py-4 flex items-center gap-4 text-gray-600">
            <a href="{{ route('dashboard.user') }}" class="hover:text-blue-600 transition">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center">
                    <i class="fa-solid fa-pencil text-sm"></i>
                </div>
                <span class="font-semibold text-gray-900">Materi Pembelajaran</span>
            </div>
        </div>
    </div>

    <main class="container mx-auto px-6 py-8">
        
        {{-- JUDUL HALAMAN --}}
        {{-- Karena di controller kamu mengambil semua materi (mixed), kita beri judul umum --}}
        {{-- Tapi kalau kamu mau ambil nama Course dari materi pertama, bisa pakai logika di bawah --}}
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                {{-- Cara ambil judul course (jika ada datanya) --}}
                {{ $materials->first()->course->title ?? 'Daftar Semua Materi' }}
            </h1>
            <p class="text-gray-500 text-lg">
                Unduh materi pembelajaran untuk dipelajari secara offline.
            </p>
        </div>

        {{-- GRID SYSTEM (LOOPING DATA) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- MULAI LOOPING DATABASE --}}
            @forelse($materials as $material)
                
                <div class="border border-gray-200 rounded-2xl p-6 flex gap-5 shadow-sm hover:shadow-md transition bg-white items-start">
                    
                    {{-- LOGIKA ICON: Beda icon kalau PDF atau Video (Optional) --}}
                    <div class="w-12 h-12 rounded-xl border border-gray-300 flex items-center justify-center flex-shrink-0 
                        {{ Str::contains(strtolower($material->title), 'video') ? 'bg-blue-50 border-blue-200 text-blue-500' : 'text-gray-600' }}">
                        
                        @if(Str::contains(strtolower($material->title), 'video'))
                            <i class="fa-solid fa-video text-xl"></i>
                        @else
                            <i class="fa-regular fa-file-lines text-xl"></i>
                        @endif
                    </div>

                    <div class="flex-1">
                        {{-- 1. JUDUL MATERI DARI DB --}}
                        <h3 class="font-bold text-lg text-gray-900 line-clamp-1" title="{{ $material->title }}">
                            {{ $material->title }}
                        </h3>

                        {{-- 2. NAMA COURSE (Biar tau ini materi mapel apa) --}}
                        <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded mt-1 inline-block">
                            {{ $material->course->title ?? 'Umum' }}
                        </span>

                        {{-- 3. DESKRIPSI DARI DB --}}
                        <p class="text-gray-500 text-sm mt-2 leading-relaxed line-clamp-2">
                            {{ $material->description ?? 'Tidak ada deskripsi tambahan untuk materi ini.' }}
                        </p>
                        
                        <div class="flex items-center justify-between mt-4">
                            {{-- Ukuran File (Kalau tidak ada di DB, kita hide atau kasih random) --}}
                            <span class="text-gray-400 text-sm">
                                <i class="fa-solid fa-download"></i> File
                            </span>

                            {{-- 4. TOMBOL DOWNLOAD (Route ke Controller) --}}
                            <a href="{{ route('material.download', $material->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-4 rounded-full flex items-center gap-2 transition no-underline">
                                <i class="fa-regular fa-file-pdf"></i> Unduh
                            </a>
                        </div>
                    </div>
                </div>

            @empty
                {{-- TAMPILAN JIKA KOSONG --}}
                <div class="col-span-full text-center py-10">
                    <div class="inline-block p-4 rounded-full bg-gray-100 mb-3">
                        <i class="fa-solid fa-folder-open text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada materi</h3>
                    <p class="text-gray-500">Silakan cek kembali nanti.</p>
                </div>
            @endforelse

        </div>

        {{-- PAGINATION (Link Halaman 1, 2, 3...) --}}
        <div class="mt-8 flex justify-center">
            {{ $materials->links() }}
        </div>

    </main>

</body>
</html>