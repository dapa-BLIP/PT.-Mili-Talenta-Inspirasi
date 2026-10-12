<!-- ALERT NOTIFIKASI AJAX -->
<div id="ajax-notification" class="hidden">
    <div id="ajax-notif-box" class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs flex items-center justify-between rounded-xs">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5 rounded-xs">OK</span>
            <p id="ajax-notif-msg" class="text-[10px] font-bold tracking-wide uppercase"></p>
        </div>
        <button type="button" onclick="document.getElementById('ajax-notification').classList.add('hidden')" class="text-emerald-600 font-bold hover:text-emerald-800 cursor-pointer border-none bg-transparent">&times;</button>
    </div>
</div>

<!-- FLASH SESSION NOTIFICATION -->
@if(session('success'))
    <div class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs flex items-center justify-between rounded-xs">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5 rounded-xs">OK</span>
            <p class="text-[10px] font-bold tracking-wide uppercase">{{ session('success') }}</p>
        </div>
        <button type="button" class="text-emerald-600 font-black text-sm cursor-pointer hover:text-emerald-800 border-none bg-transparent" onclick="this.parentElement.remove()">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="px-4 py-2 bg-rose-50 border border-rose-200 text-rose-800 shadow-xs flex items-center justify-between rounded-xs">
        <div class="space-y-0.5">
            <span class="text-[10px] font-black uppercase bg-rose-600 text-white px-1.5 py-0.5 rounded-xs">ERR</span>
            @foreach ($errors->all() as $err)
                <p class="text-[10px] font-bold tracking-wide uppercase mt-0.5">* {{ $err }}</p>
            @endforeach
        </div>
        <button type="button" class="text-rose-600 font-black text-sm cursor-pointer hover:text-rose-800 border-none bg-transparent" onclick="this.parentElement.remove()">&times;</button>
    </div>
@endif
