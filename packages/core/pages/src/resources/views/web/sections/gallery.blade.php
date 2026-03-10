<!-- Gallery Section -->
<section class="gallery-section">
    <div class="container">
        <div class="gallery-grid">
            @foreach($section->images_urls as $image)
            <div class="gallery-item item-{{ $loop->iteration }}">
                <div class="gallery-image">
                    <img src="{{ $image }}" alt="Face massage therapy">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section> 