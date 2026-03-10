<!-- Footer Section -->
<footer class="footer-section">
    <div class="container">
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-brand">
                    <h2 class="brand-footer "><img src="{{config('app.logo')}}" alt=""></h2>
                    <p class="footer-description">
                        {{settings('description')}}
                    </p>
                    <div class="footer-social">
                        <a href="https://wa.me/{{settings('whatsapp')}}" class="footer-social-btn">
                            <ion-icon name="logo-whatsapp"></ion-icon>
                        </a>
                        <a href="https://www.youtube.com/{{settings('youtube')}}" class="footer-social-btn">
                            <ion-icon name="logo-youtube"></ion-icon>
                        </a>
                        <a href="{{settings('facebook')}}" class="footer-social-btn">
                            <ion-icon name="logo-facebook"></ion-icon>
                        </a>
                        <a href="{{settings('instagram')}}" class="footer-social-btn">
                            <ion-icon name="logo-instagram"></ion-icon>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-right">
                <div class="contact-info">
                    <h3 class="contact-title">{{ trans('call_us') }}</h3>
                    <div class="contact-list">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <ion-icon name="call"></ion-icon>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">{{ trans('phone') }}</span>
                                <span class="contact-value">{{ settings('phone') }}</span>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <ion-icon name="mail"></ion-icon>
                            </div>
                            <div class="contact-details">
                                <span class="contact-label">{{ trans('email') }}</span>
                                <span class="contact-value">{{ settings('email') }}</span>
                            </div>
                        </div>

                        <div class="address-section">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <ion-icon name="location"></ion-icon>
                                </div>
                                <div class="contact-details">
                                    <span class="contact-label">{{ trans('address') }}</span>
                                    <div class="address-content">
                                        <span class="contact-value">{{ settings('address') }}</span>
                                        <a href="{{settings('map')}}" class="show-map-btn">{{ trans('show_map') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-divider"></div>
            <p class="footer-copyright">
                {{ trans('all_rights_reserved') }} {{ date('Y') }}. {{ trans('crafted_by') }} {{ settings('name') }}.
            </p>
        </div>
    </div>
</footer> 