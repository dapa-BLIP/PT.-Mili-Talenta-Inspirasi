<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Portal System | PT Mili Talenta Inspirasi</title>
    
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet" />
    <style> [x-cloak] { display: none !important; } </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#f4f7f9] text-slate-700" x-data="{ openLogout: false, openMobileSidebar: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- ================================================================= -->
        <!-- 1. BACKDROP SIDEBAR MOBILE (BERSIH TANPA GLITCH BLUR)             -->
        <!-- ================================================================= -->
        <div x-show="openMobileSidebar" 
             x-cloak 
             @click="openMobileSidebar = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 z-[250] md:hidden">
        </div>

        <!-- ================================================================= -->
        <!-- 2. SIDEBAR NAVIGATION: HYBRID                                     -->
        <!-- ================================================================= -->
        <aside :class="openMobileSidebar ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-[260] w-64 bg-[#1a2226] flex flex-col shrink-0 border-r border-slate-800 select-none transition-transform duration-300 ease-in-out md:static md:z-auto">
            
            <!-- Header Sidebar & Logo -->
            <div class="h-12 sm:h-14 flex items-center justify-between px-4 sm:px-5 bg-[#171e22] border-b border-white/5 shrink-0">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-mili.jpg') }}" alt="Logo PT Mili" class="h-7 w-7 sm:h-8 sm:w-8 object-contain rounded-xs shrink-0" onerror="this.style.display='none'">
                    <div class="flex flex-col leading-none">
                        <span class="text-white font-black tracking-tight text-xs sm:text-sm uppercase">Portal <span class="text-[#22a0f6]">Mili</span></span>
                        <span class="text-[7.5px] sm:text-[8px] text-slate-500 font-bold tracking-widest uppercase mt-0.5">Internal System</span>
                    </div>
                </div>

                <button type="button" 
                        @click="openMobileSidebar = false" 
                        class="md:hidden text-slate-400 hover:text-white p-1 rounded-xs cursor-pointer leading-none text-xl" 
                        title="Tutup Menu">
                    &times;
                </button>
            </div>

            <!-- Navigasi Menu Utama -->
            <nav class="flex-1 px-3 space-y-1 mt-3 sm:mt-4 overflow-y-auto">
                <p class="px-3 text-[8.5px] sm:text-[9px] font-black text-slate-500/80 uppercase tracking-[0.2em] mb-2 sm:mb-3">Main Navigation</p>
                
                <!-- 1. DASHBOARD -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 sm:gap-3 px-3.5 py-2 sm:py-2.5 rounded-xs transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-[#22a0f6] text-white font-black shadow-xs shadow-blue-500/20' : 'text-slate-400 hover:bg-[#222d32] hover:text-white' }}">
                    <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-[9.5px] sm:text-[10px] font-black uppercase tracking-widest">Dashboard</span>
                </a>

                <!-- 2. DROPDOWN: MASTER AKADEMIK -->
                <div x-data="{ openAcademic: {{ request()->routeIs('academic.*') ? 'true' : 'false' }} }">
                    <button @click="openAcademic = !openAcademic" class="w-full flex items-center justify-between gap-2.5 sm:gap-3 px-3.5 py-2 sm:py-2.5 rounded-xs transition-all duration-200 cursor-pointer {{ request()->routeIs('academic.*') ? 'bg-[#222d32] text-white font-black' : 'text-slate-400 hover:bg-[#222d32] hover:text-white' }}">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span class="text-[9.5px] sm:text-[10px] font-black uppercase tracking-widest">Master Akademik</span>
                        </div>
                        <svg :class="{'rotate-180': openAcademic}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"></path></svg>
                    </button>
                    
                    <div x-show="openAcademic" x-cloak class="bg-[#161d21] py-1 border-l-2 border-[#22a0f6] ml-3.5 transition-all">
                        <a href="{{ route('academic.mentor.index') }}" class="block px-3.5 py-1.5 text-[8.5px] sm:text-[9px] font-bold uppercase tracking-widest {{ request()->routeIs('academic.mentor.*') ? 'text-[#22a0f6] font-black' : 'text-slate-400 hover:text-white' }}">
                            Kelola Mentor
                        </a>
                        <a href="{{ route('academic.pelatihan.index') }}" class="block px-3.5 py-1.5 text-[8.5px] sm:text-[9px] font-bold uppercase tracking-widest {{ request()->routeIs('academic.pelatihan.*') ? 'text-[#22a0f6] font-black' : 'text-slate-400 hover:text-white' }}">
                            Kelola Pelatihan
                        </a>
                    </div>
                </div>

                <!-- 3. DROPDOWN: MANAJEMEN PESERTA -->
                <div x-data="{ openPeserta: {{ request()->routeIs('peserta.*', 'nilai.*', 'perusahaan.*') ? 'true' : 'false' }} }">
                    <button @click="openPeserta = !openPeserta" class="w-full flex items-center justify-between gap-2.5 sm:gap-3 px-3.5 py-2 sm:py-2.5 rounded-xs transition-all duration-200 cursor-pointer {{ request()->routeIs('peserta.*', 'nilai.*', 'perusahaan.*') ? 'bg-[#222d32] text-white font-black' : 'text-slate-400 hover:bg-[#222d32] hover:text-white' }}">
                        <div class="flex items-center gap-2.5 sm:gap-3">
                            <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="text-[9.5px] sm:text-[10px] font-black uppercase tracking-widest">Manajemen Peserta</span>
                        </div>
                        <svg :class="{'rotate-180': openPeserta}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"></path></svg>
                    </button>
                    
                    <div x-show="openPeserta" x-cloak class="bg-[#161d21] py-1 border-l-2 border-[#22a0f6] ml-3.5 transition-all">
                        <a href="{{ route('perusahaan.index') }}" class="block px-3.5 py-1.5 text-[8.5px] sm:text-[9px] font-bold uppercase tracking-widest {{ request()->routeIs('perusahaan.*') ? 'text-[#22a0f6] font-black' : 'text-slate-400 hover:text-white' }}">
                            Kelola Perusahaan
                        </a>
                        <a href="{{ route('peserta.index') }}" class="block px-3.5 py-1.5 text-[8.5px] sm:text-[9px] font-bold uppercase tracking-widest {{ request()->routeIs('peserta.*') ? 'text-[#22a0f6] font-black' : 'text-slate-400 hover:text-white' }}">
                            Kelola Peserta
                        </a>
                        <a href="{{ route('nilai.index') }}" class="block px-3.5 py-1.5 text-[8.5px] sm:text-[9px] font-bold uppercase tracking-widest {{ request()->routeIs('nilai.*') ? 'text-[#22a0f6] font-black' : 'text-slate-400 hover:text-white' }}">
                            Kelola Nilai
                        </a>
                    </div>
                </div>

                <!-- 4. JADWAL INTERNAL -->
                <a href="{{ route('jadwal.index') }}" class="flex items-center gap-2.5 sm:gap-3 px-3.5 py-2 sm:py-2.5 rounded-xs transition-all duration-200 group {{ request()->routeIs('jadwal.*') ? 'bg-[#22a0f6] text-white font-black shadow-xs shadow-blue-500/20' : 'text-slate-400 hover:bg-[#222d32] hover:text-white' }}">
                    <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-[9.5px] sm:text-[10px] font-black uppercase tracking-widest">Jadwal Internal</span>
                </a>

                <!-- 5. SYSTEM FEEDBACK -->
                <a href="{{ route('feedback.index') }}" class="flex items-center gap-2.5 sm:gap-3 px-3.5 py-2 sm:py-2.5 rounded-xs transition-all duration-200 group {{ request()->routeIs('feedback.*') ? 'bg-[#22a0f6] text-white font-black shadow-xs shadow-blue-500/20' : 'text-slate-400 hover:bg-[#222d32] hover:text-white' }}">
                    <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <span class="text-[9.5px] sm:text-[10px] font-black uppercase tracking-widest">System Feedback</span>
                </a>
            </nav>

            <!-- Profile & Sign Out Button -->
            <div class="p-3 sm:p-3.5 bg-[#182023] border-t border-white/5 shrink-0">
                <div class="flex items-center gap-2.5 mb-2.5 sm:mb-3 px-1 select-none">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xs bg-[#22a0f6] flex items-center justify-center text-[9px] sm:text-[10px] font-black text-white shadow-xs shadow-blue-500/20 shrink-0">
                        {{ substr(Auth::user()?->name ?? 'AD', 0, 2) }}
                    </div>
                    <div class="truncate">
                        <p class="text-[9.5px] sm:text-[10px] font-black text-white uppercase truncate">{{ Auth::user()?->name ?? 'Administrator' }}</p>
                        <p class="text-[7.5px] sm:text-[8px] font-bold text-[#22a0f6] uppercase tracking-widest italic mt-0.5">Administrator</p>
                    </div>
                </div>
                <button @click="openLogout = true" class="w-full py-1.5 sm:py-2 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white rounded-xs transition-all duration-200 text-[8.5px] sm:text-[9px] font-black uppercase tracking-[0.2em] cursor-pointer">
                    Sign Out
                </button>
            </div>
        </aside>

        <!-- ================================================================= -->
        <!-- 3. MAIN CONTAINER (HEADER TANPA Z-10 AGAR TIDAK BOCOR KE MODAL)   -->
        <!-- ================================================================= -->
        <main class="flex-1 h-screen overflow-hidden flex flex-col bg-[#f8fafc]">
            
            <!-- HEADER APP (Z-10 DIHAPUS) -->
            @if (isset($header))
                <header class="bg-white border-b border-slate-200 px-3 sm:px-6 lg:px-8 h-12 sm:h-14 flex items-center gap-2 sm:gap-3 shadow-xs shrink-0">
                    <button type="button" 
                            @click="openMobileSidebar = true" 
                            class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xs md:hidden cursor-pointer shrink-0 transition-colors" 
                            title="Buka Menu Navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <div class="flex-1 min-w-0">{{ $header }}</div>
                </header>
            @else
                <!-- Fallback Header Mobile (Z-10 DIHAPUS) -->
                <header class="md:hidden bg-white border-b border-slate-200 px-3 h-12 flex items-center gap-2 shadow-xs shrink-0">
                    <button type="button" 
                            @click="openMobileSidebar = true" 
                            class="p-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xs cursor-pointer shrink-0 transition-colors" 
                            title="Buka Menu Navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="text-xs font-black text-slate-700 uppercase tracking-wider">Portal Mili</span>
                </header>
            @endif
            
            <!-- AREA KONTEN -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-5 lg:p-6">
                {{ $slot }}
            </div>
            
            <!-- FOOTER -->
            <footer class="px-4 sm:px-6 lg:px-8 py-2.5 sm:py-3 bg-white border-t border-slate-200 flex justify-between items-center text-[8.5px] sm:text-[9px] font-black uppercase tracking-widest text-slate-400 shrink-0 select-none">
                <p>System Status: <span class="text-emerald-500 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Online</span></p>
                <p>MTI Portal &copy; 2026</p>
            </footer>
        </main>
    </div>

    <!-- ===================================================================== -->
    <!-- 4. MODAL LOGOUT (OVERLAY BERSIH)                                      -->
    <!-- ===================================================================== -->
    <div x-show="openLogout" x-cloak class="fixed inset-0 z-[300] flex items-center justify-center p-4 transition-all duration-300">
        <div @click="openLogout = false" class="absolute inset-0 bg-slate-900/60"></div>
        <div class="relative bg-white w-full max-w-[300px] p-5 rounded-xs shadow-2xl border border-slate-200">
            <div class="text-center">
                <div class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xs flex items-center justify-center mx-auto mb-3 border border-rose-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-tighter">Konfirmasi Keluar</h3>
                <p class="text-[9.5px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Apakah Anda yakin ingin mengakhiri sesi?</p>
            </div>
            <div class="mt-5 flex gap-2">
                <button @click="openLogout = false" class="flex-1 px-3 py-1.5 border border-slate-200 text-slate-500 text-[8.5px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all rounded-xs cursor-pointer">Batal</button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-3 py-1.5 bg-rose-500 text-white text-[8.5px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all rounded-xs shadow-xs cursor-pointer border-none">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>