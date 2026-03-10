<div class="nav-container">
    <nav class="navbar">
        <a href="{{ route('home') }}" class="brand"><img src="{{config('app.logo')}}" alt=""></a>

        <ul class="nav-menu" id="navMenu">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">{{ trans('home') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('about-us') }}" class="nav-link">{{ trans('about_us') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('services') }}" class="nav-link">{{ trans('our_services') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('blog') }}" class="nav-link">{{ trans('our_blogs') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contact-us') }}" class="nav-link">{{ trans('contact_us') }}</a>
            </li>
        <li class="nav-item dropdown language-dropdown" style="position: relative;">
            <a href="#" class="nav-link dropdown-toggle" id="languageDropdown" role="button" aria-haspopup="true" aria-expanded="false" style="cursor:pointer;">
                {{ strtoupper(app()->getLocale()) }}
                <span style="margin-left: 4px;">&#9662;</span>
            </a>
            <div class="dropdown-menu" id="languageDropdownMenu" style="
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                min-width: 120px;
                background: #fff;
                border: 1px solid #ddd;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                z-index: 1000;
                padding: 0.5rem 0;
                border-radius: 4px;
            ">
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    @if($localeCode !== app()->getLocale())
                        <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" style="
                            display: block;
                            padding: 0.5rem 1rem;
                            color: #333;
                            text-decoration: none;
                            white-space: nowrap;
                        " onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">
                            {{ $properties['native'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </li>
        <script>
            // Toggle dropdown on click
            document.addEventListener('DOMContentLoaded', function() {
                var toggle = document.getElementById('languageDropdown');
                var menu = document.getElementById('languageDropdownMenu');

                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (menu.style.display === 'block') {
                        menu.style.display = 'none';
                    } else {
                        menu.style.display = 'block';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!toggle.contains(event.target) && !menu.contains(event.target)) {
                        menu.style.display = 'none';
                    }
                });
            });
        </script>
        </ul>

        <div class="d-flex">
            <a href="#" class="book-btn">{{ trans('book_now') }}</a>
            <button class="mobile-toggle" onclick="toggleMobileMenu()">☰</button>
        </div>
    </nav>
</div> 