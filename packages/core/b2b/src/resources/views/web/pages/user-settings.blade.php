@extends('b2b::web.layouts.app')

@section('content')
    <!-- VIEW: Account Settings -->
    <div id="view-account_settings" class="view-section active max-w-3xl mx-auto space-y-6 mt-4">
        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100">
            <h2 class="text-2xl font-black text-gray-900 mb-8 text-right dir-dependent-text" data-i18n="account_settings">
                {{ $title }}
            </h2>

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 font-bold text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 font-bold text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6 text-right dir-dependent-text"
                action="{{ route('client.profile.update-profile.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1"
                        data-i18n="hotel_logo">{{ trans('image') }}</label>
                    <div
                        class="flex items-center gap-4 border border-gray-200 rounded-xl p-3 bg-gray-50 dir-dependent-flex">
                        <img src="{{ $user->avatar_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&q=80&w=150&h=150' }}"
                            alt="logo" class="h-12 w-12 rounded-lg object-cover shadow-sm profile-photo-preview" />
                        <label
                            class="bg-white border border-gray-200 px-5 py-2 text-xs font-bold rounded-lg shadow-sm hover:bg-gray-50 transition-colors cursor-pointer">
                            {{ trans('edit') }}
                            <input type="file" name="avatar" class="hidden" onchange="previewImage(this)">
                        </label>
                    </div>
                    @error('avatar')
                        <p class="text-red-500 text-xs font-bold mt-1 px-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label
                        class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">{{ trans('fullname') }}</label>
                    <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}"
                        class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] focus:bg-white font-bold transition-colors text-right dir-dependent-text"
                        required />
                    @error('fullname')
                        <p class="text-red-500 text-xs font-bold mt-1 px-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label
                        class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">{{ trans('email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" dir="ltr"
                        class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] focus:bg-white font-bold transition-colors text-left"
                        required />
                    @error('email')
                        <p class="text-red-500 text-xs font-bold mt-1 px-1 text-right">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label
                        class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">{{ trans('phone') }}</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" dir="ltr"
                        class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] focus:bg-white font-bold transition-colors text-left"
                        required />
                    @error('phone')
                        <p class="text-red-500 text-xs font-bold mt-1 px-1 text-right">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Line of Business -->
              
                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-gray-900 hover:bg-black text-white px-8 py-4 rounded-xl font-black shadow-lg transition-transform hover:-translate-y-0.5">
                        <span data-i18n="save_changes">{{ trans('save_changes') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-8 md:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 mt-8">
            <h2 class="text-2xl font-black text-gray-900 mb-8 text-right dir-dependent-text">
                {{ trans('client.password_data') }}
            </h2>

            <form class="space-y-6 text-right dir-dependent-text"
                action="{{ route('client.profile.update-password') }}" method="POST">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">{{ trans('client.new_password') }}</label>
                    <input type="password" name="password" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] focus:bg-white font-bold transition-colors text-left" required />
                    @error('password')
                        <p class="text-red-500 text-xs font-bold mt-1 px-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest px-1">{{ trans('client.confirm_new_password') }}</label>
                    <input type="password" name="password_confirmation" class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-[#1c75bc] focus:bg-white font-bold transition-colors text-left" required />
                </div>
                <div class="pt-6">
                    <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white px-8 py-4 rounded-xl font-black shadow-lg transition-transform hover:-translate-y-0.5">
                        {{ trans('client.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector('.profile-photo-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection