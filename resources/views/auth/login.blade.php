<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Selamat Datang</h2>
        <p class="text-xs text-gray-400 mt-1">Silakan masuk untuk mengakses dashboard internal.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5 tracking-wide">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                class="block w-full px-3.5 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-[#22a0f6] focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all text-gray-800 placeholder-gray-300" 
                placeholder="nama@mili-talenta.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5 tracking-wide">Password</label>
            <input id="password" type="password" name="password" required 
                class="block w-full px-3.5 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-[#22a0f6] focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all text-gray-800 placeholder-gray-400" 
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
        </div>

        <div class="flex items-center justify-between pt-1 text-xs">
            <label for="remember_me" class="flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#22a0f6] focus:ring-0 cursor-pointer">
                <span class="ms-2 text-gray-500 font-medium">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-[#22a0f6] hover:text-blue-600 font-medium transition-colors" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-[#22a0f6] hover:bg-blue-600 text-white py-2.5 rounded-lg text-sm font-semibold tracking-wide transition-all duration-200 shadow-md shadow-blue-500/10">
                Masuk ke Portal
            </button>
        </div>

        <div class="text-center pt-4 border-t border-gray-100 mt-4">
            <p class="text-xs text-gray-400 font-medium">
                Belum memiliki akses? <a href="{{ route('register') }}" class="text-[#22a0f6] hover:text-blue-600 hover:underline font-bold transition-all ml-0.5">Daftar Akun</a>
            </p>
        </div>
    </form>
</x-guest-layout>