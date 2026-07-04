<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>PT Mili | Gateway</title>
        
        <!-- PANGGIL FONT RALEWAY & FIGTREE -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..0,900;1,100..0,900&display=swap" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <!-- 🎨 BODY: Full Putih Bersih Minimalis -->
    <body class="bg-white font-sans antialiased min-h-screen flex items-center justify-center">
        
        <!-- 📝 UPDATE MOBILE: Ditambahkan 'flex flex-col md:grid' agar di HP posisinya ngalir ke bawah dengan rapi -->
        <div class="w-full min-h-screen flex flex-col md:grid md:grid-cols-2 bg-white">
            
            <!-- ========================================================================= -->
            <!-- SISI KIRI: PANEL VISUAL BRAND PT MILI (DESKTOP ONLY)                      -->
            <!-- ========================================================================= -->
            <div class="hidden md:flex flex-col justify-center p-24 bg-white min-h-screen relative">
                
                <!-- 🛠️ BLOK IKATAN KONTEN: Mengikat semua elemen biar tetep rapat, tapi posisinya pas di tengah layar -->
                <div class="w-full max-w-xl space-y-6 select-none">
                    
                    <!-- 1. BADGE INTERNAL PORTAL: Jarak rapat ke bawah -->
                    <div class="block">
                        <span class="text-[9px] font-black tracking-[0.25em] uppercase bg-[#22a0f6] text-white px-3 py-1.5 rounded-lg font-mono shadow-sm shadow-blue-500/10 border border-blue-400/20">
                            INTERNAL PORTAL
                        </span>
                    </div>
                    
                    <!-- 2. BRAND BLOCK: Logo & Nama PT Sejajar Horizontal -->
                    <div class="flex items-center gap-4 w-full pt-2">
                        <!-- Logo pas proporsional -->
                        <img src="{{ asset('images/logo-mili.jpg') }}" alt="Logo PT Mili" class="h-16 w-16 object-contain block shrink-0">
                        
                        <!-- Nama PT memanjang lurus -->
                        <h1 class="text-4xl font-black tracking-tight leading-tight text-[#22a0f6]" style="font-family: 'Raleway', sans-serif;">
                            PT. Mili Talenta Inspirasi
                        </h1>
                    </div>

                    <!-- 3. LINE & DESKRIPSI -->
                    <div class="space-y-4">
                        <div class="h-[3px] w-14 bg-[#22a0f6]/60"></div>
                        <p class="text-sm text-slate-500 font-medium max-w-sm leading-relaxed">
                            Sistem Informasi Manajemen Data Peserta dan Dashboard Penilaian Terintegrasi.
                        </p>
                    </div>
                    
                </div>

                <!-- FOOTER COMPANY CONTENT: Tetap mengunci rapi di bawah area kiri -->
                <div class="absolute bottom-8 left-24 text-[11px] text-[#22a0f6]/70 font-mono tracking-wide leading-relaxed select-none">
                    &copy; 2026 PT Mili Talenta Inspirasi. <br>
                    <span class="text-[#22a0f6] font-black">Made by Politeknik Mitra Industri</span>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- SISI KANAN: CONTAINER FORM LOGIN & REGISTER + LOGO MOBILE EMERGENCY       -->
            <!-- ========================================================================= -->
            <!-- 📝 UPDATE MOBILE: Ditambahkan 'justify-center py-12 md:py-6' biar di HP gak mentok atas-bawah -->
            <div class="flex flex-col items-center justify-center p-6 sm:p-12 bg-white flex-1 min-h-screen md:min-h-0">
                
                <!-- 🚀 LOGO MOBILE EMERGENCY: Hanya muncul di HP (md:hidden), posisi center manis -->
                <div class="flex md:hidden flex-col items-center gap-3 mb-8 text-center select-none pt-4">
                    <img src="{{ asset('images/logo-mili.jpg') }}" alt="Logo PT Mili" class="h-14 w-14 object-contain">
                    <h2 class="text-xl font-black text-[#22a0f6]" style="font-family: 'Raleway', sans-serif;">PT. Mili Talenta Inspirasi</h2>
                    <span class="text-[9px] font-black tracking-[0.15em] bg-[#22a0f6] text-white px-2 py-1 rounded font-mono">INTERNAL PORTAL</span>
                </div>

                <!-- CARD FORM: Background putih dengan shadow halus premium -->
                <div class="w-full max-w-sm bg-white p-10 rounded-2xl shadow-[0_8px_40px_rgba(0,0,0,0.05)] border border-slate-100 transition-all">
                    {{ $slot }}
                </div>
                
                <!-- 🚀 FOOTER KHUSUS MOBILE: Biar pas di HP hak ciptanya tetep keliatan di paling bawah -->
                <div class="block md:hidden text-center text-[10px] text-slate-400 font-mono mt-8 select-none">
                    &copy; 2026 PT Mili Talenta Inspirasi. <br>
                    <span class="text-slate-500 font-bold">Made by Politeknik Mitra Industri</span>
                </div>
            </div>

        </div>

    </body>
</html>