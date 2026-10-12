<!-- FILTER FORM PANEL (MENGIRIM PARAMETER GET KE CONTROLLER) -->
<div class="bg-white border border-slate-200 rounded-sm shadow-sm p-3.5 sm:p-4 mb-4 print:hidden">
    <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
        <div class="sm:col-span-2 lg:col-span-2">
            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Jadwal Monitor</label>
            <div class="bg-slate-900 border border-slate-800 rounded-sm px-3 flex items-center justify-between h-[31px] tabular-nums">
                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                <span class="text-[11px] font-black text-blue-400 bg-slate-950/50 px-2 py-0.5 rounded-sm border border-slate-800">
                    {{ sprintf('%02d', count($jadwals ?? [])) }}
                </span>
            </div>
        </div>

        <div class="sm:col-span-1 lg:col-span-2">
            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tanggal mulai</label>
            <input type="date" id="filter_start_date" name="start_date" value="{{ request('start_date') }}" 
                class="w-full bg-slate-50 border border-slate-200 rounded-sm px-3 py-1.5 h-[31px] text-[10px] font-bold text-slate-700 uppercase tracking-tight focus:outline-none focus:border-[#22a0f6] transition-colors">
        </div>
        
        <div class="sm:col-span-1 lg:col-span-2">
            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Tanggal selesai</label>
            <input type="date" id="filter_end_date" name="end_date" value="{{ request('end_date') }}" 
                class="w-full bg-slate-50 border border-slate-200 rounded-sm px-3 py-1.5 h-[31px] text-[10px] font-bold text-slate-700 uppercase tracking-tight focus:outline-none focus:border-[#22a0f6] transition-colors">
        </div>

        <div class="sm:col-span-1 lg:col-span-2">
            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Piih Mentor</label>
            <select name="trainer_id" class="w-full bg-slate-50 border border-slate-200 rounded-sm px-2 py-1 h-[31px] text-[10px] font-bold text-slate-700 uppercase focus:outline-none focus:border-[#22a0f6] transition-colors cursor-pointer">
                <option value="">-Semua Mentor-</option>
                @foreach($trainers ?? [] as $trainer)
                    <option value="{{ $trainer->id }}" {{ request('trainer_id') == $trainer->id ? 'selected' : '' }}>
                        {{ strtoupper($trainer->nama_mentor) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-1 lg:col-span-2">
            <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Select Status</label>
            <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-sm px-2 py-1 h-[31px] text-[10px] font-bold text-slate-700 uppercase focus:outline-none focus:border-[#22a0f6] transition-colors cursor-pointer">
                <option value="">-Semua Status-</option>
                <option value="on-going" {{ request('status') == 'on-going' ? 'selected' : '' }}>ON-GOING</option>
                <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>COMPLETE</option>
            </select>
        </div>
        
        <div class="flex gap-2 sm:col-span-2 lg:col-span-2">
            <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white h-[31px] rounded-sm text-[9px] font-black uppercase tracking-widest transition-all shadow-sm border-none cursor-pointer">Submit</button>
            @if(request('start_date') || request('end_date') || request('trainer_id') || request('status'))
                <a href="{{ url()->current() }}" class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-400 px-2.5 h-[31px] rounded-sm text-[9px] font-black uppercase tracking-widest transition-all text-center flex items-center justify-center decoration-none">Clear</a>
            @endif
        </div>
    </form>
</div>

