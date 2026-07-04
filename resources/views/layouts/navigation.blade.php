<aside class="w-64 bg-[#1a2226] flex flex-col shrink-0 border-r border-slate-800 h-screen sticky top-0">
    <!-- HEADER BAR: Logo & Judul Sistem Resmi PT MILI -->
    <div class="h-16 flex items-center px-6 gap-2.5 border-b border-white/5 select-none">
        <img src="{{ asset('images/logo-mili.jpg') }}" alt="Logo PT Mili" class="h-8 w-8 object-contain">
        <span class="text-white font-black text-[11px] uppercase tracking-wider leading-tight">
            PT MILI <br><span class="text-[#22a0f6]">TALENTA INSPIRASI</span>
        </span>
    </div>

    <!-- CORE NAVIGATION HUB -->
    <nav class="flex-1 px-4 space-y-1 mt-8">
        <p class="px-4 text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Main Navigation</p>
        
        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 border-l-2 {{ request()->routeIs('dashboard') ? 'bg-[#222d32] text-white border-[#22a0f6]' : 'border-transparent text-slate-400 hover:bg-[#222d32] hover:text-white hover:border-[#22a0f6]' }}">
            <span class="text-[10px] font-black uppercase tracking-widest">Dashboard</span>
        </a>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 border-l-2 border-transparent text-slate-400 hover:bg-[#222d32] hover:text-white hover:border-[#22a0f6] border-none bg-transparent cursor-pointer">
                <span class="text-[10px] font-black uppercase tracking-widest">Manajemen Peserta</span>
                <svg class="w-3 h-3 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="open" x-cloak class="bg-[#161d21] py-2">
                <a href="{{ route('peserta.index') }}" class="block px-12 py-2 text-[9px] font-black text-slate-400 hover:text-[#22a0f6] uppercase tracking-widest decoration-none">Kelola Peserta</a>
                <a href="{{ route('nilai.index') }}" class="block px-12 py-2 text-[9px] font-black text-slate-400 hover:text-[#22a0f6] uppercase tracking-widest decoration-none">Kelola Nilai</a>
            </div>
        </div>
    </nav>

    <!-- USER PANEL PROFILES FOOTER SECTION -->
    <div class="p-6 bg-[#161d21] border-t border-white/5 select-none">
        <div class="mb-4">
            <p class="text-[10px] font-black text-white uppercase">{{ Auth::user()->name }}</p>
            <p class="text-[8px] font-bold text-[#22a0f6] uppercase tracking-widest">Administrator</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-2 border border-rose-500/30 text-rose-400 hover:bg-rose-500 hover:text-white text-[9px] font-black uppercase tracking-[0.2em] transition-all bg-transparent cursor-pointer">Sign Out</button>
        </form>
    </div> 
</aside>