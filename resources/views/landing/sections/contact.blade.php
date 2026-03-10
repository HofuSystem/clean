<section id="contact" class="bg-white py-24 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-black text-gray-900 mb-4">{{ $section->title }}</h2>
            <p class="text-gray-500">{!! $section->description !!}</p>
        </div>
        <div class="grid lg:grid-cols-2 gap-12">
            <div class="bg-brand-900 text-white p-10 rounded-3xl relative overflow-hidden" data-aos="fade-right">
                <div class="relative z-10 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center"><i
                                class="fa-brands fa-whatsapp"></i></div>
                        <div class="text-xl font-bold" dir="ltr"><a href="https://wa.me/{{ setting('whatsapp') }}" target="_blank">{{ setting('whatsapp') }}</a></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center"><i
                                class="fa-solid fa-envelope"></i></div>
                        <div class="text-xl font-bold"><a href="mailto:{{ setting('email') }}" target="_blank">{{ setting('email') }}</a></div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center"><i
                                class="fa-solid fa-location-dot"></i></div>
                        <div class="text-lg">{{ setting('address') }}</div>
                    </div>
                </div>
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="bg-gray-50 p-10 rounded-3xl border border-gray-100" data-aos="fade-left">
                @if (session('contact_success'))
                    <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-4 text-center font-bold">
                        {{ session('contact_success') }}</div>
                @endif
                <form action="{{ route('contact') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="text" name="name" placeholder="{{ trans('name') }}"
                        class="w-full p-4 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-brand-500 outline-none"
                        required>
                    <input type="text" name="phone" placeholder="{{ trans('phone') }}"
                        class="w-full p-4 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-brand-500 outline-none"
                        required>
                    <input type="email" name="email" placeholder="{{ trans('email') }}"
                        class="w-full p-4 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-brand-500 outline-none"
                        required>
                    <textarea name="notes" rows="4" placeholder="{{ trans('notes') }}"
                        class="w-full p-4 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
                    <button type="submit" class="w-full bg-brand-600 text-white py-4 rounded-xl font-bold hover:bg-brand-700 transition shadow-lg">{{ trans('send') }}</button>
                </form>
            </div>
        </div>
    </div>
</section>
