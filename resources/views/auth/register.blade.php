<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Daftar Akun</h2>
        <p class="text-xs text-gray-400 mt-1">Buat akun baru untuk mengakses sistem internal.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <label for="name" class="block text-xs font-semibold text-gray-700 mb-1.5 tracking-wide">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                class="block w-full px-3.5 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-[#22a0f6] focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all text-gray-800 placeholder-gray-300" 
                placeholder="Nama Lengkap Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-500" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5 tracking-wide">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                class="block w-full px-3.5 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-[#22a0f6] focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all text-gray-800 placeholder-gray-300" 
                placeholder="nama@mili-talenta.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5 tracking-wide">Password</label>
            <input id="password" type="password" name="password" required 
                class="block w-full px-3.5 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-[#22a0f6] focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all text-gray-800 placeholder-gray-400" 
                placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1.5 tracking-wide">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required 
                class="block w-full px-3.5 py-2 bg-gray-50 border border-gray-200 text-sm rounded-lg focus:outline-none focus:border-[#22a0f6] focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all text-gray-800 placeholder-gray-400" 
                placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-500" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full bg-[#22a0f6] hover:bg-blue-600 text-white py-2.5 rounded-lg text-sm font-semibold tracking-wide transition-all duration-200 shadow-md shadow-blue-500/10">
                Daftar Sekarang
            </button>
        </div>

        <!-- Link Login -->
        <div class="text-center pt-4 border-t border-gray-100 mt-4">
            <p class="text-xs text-gray-400 font-medium">
                Sudah memiliki akses? <a href="{{ route('login') }}" class="text-[#22a0f6] hover:text-blue-600 hover:underline font-bold transition-all ml-0.5">Masuk Saja</a>
            </p>
        </div>
    </form>
</x-guest-layout>