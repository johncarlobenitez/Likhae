@php
    $slides = [
        ['image' => 'Warm Mediterranean Still Life with Terracotta Accents.png', 'alt' => 'A ceramic vase, candle, and soft throw against warm terracotta walls', 'eyebrow' => 'A LITTLE WARMTH GOES A LONG WAY', 'title' => 'Small details.', 'accent' => 'A home that feels yours.', 'description' => 'Settle into softer textures, warm accents, and comforting touches that turn your space into your favorite place.', 'cta' => 'Shop Home & Living', 'category' => 'home'],
        ['image' => 'Minimalist Workspace with City Views.png', 'alt' => 'Sunlit workspace with a laptop, desk lamp, and city views', 'eyebrow' => 'MAKE ROOM FOR BIG IDEAS', 'title' => 'Inspired spaces.', 'accent' => 'Brighter workdays.', 'description' => 'Discover tech and workspace essentials that bring focus, comfort, and a little inspiration to your everyday.', 'cta' => 'Explore Electronics', 'category' => 'electronics'],
        ['image' => 'Sunlit Sage Green Lifestyle Vignette.png', 'alt' => 'Headphones, a phone, and everyday accessories in a sunlit sage green setting', 'eyebrow' => 'LITTLE FINDS. EVERYDAY JOY.', 'title' => 'Your everyday,', 'accent' => 'beautifully chosen.', 'description' => 'From your favorite soundtrack to the things you take everywhere, find thoughtful essentials that fit your way of living.', 'cta' => 'Discover Everyday Finds', 'category' => null],
    ];
@endphp

<section class="lk-carousel" data-hero-carousel role="region" aria-roledescription="carousel" aria-label="Discover LIKHAE collections">
    <div class="lk-carousel__slides" aria-live="off">
        @foreach($slides as $slide)
            <article id="hero-slide-{{ $loop->index }}" class="lk-carousel__slide {{ $loop->first ? 'is-active' : '' }}" data-hero-slide role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ count($slides) }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}" @if(!$loop->first) inert @endif>
                <img
                    class="lk-carousel__image"
                    src="{{ asset('images/'.$slide['image']) }}"
                    alt="{{ $slide['alt'] }}"
                    width="1822"
                    height="863"
                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                    fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                    decoding="async"
                >
                <div class="lk-carousel__copy">
                    <span class="lk-carousel__eyebrow">{{ $slide['eyebrow'] }}</span>
                    @if($loop->first)
                        <h1 class="lk-carousel__title">{{ $slide['title'] }}<em>{{ $slide['accent'] }}</em></h1>
                    @else
                        <h2 class="lk-carousel__title">{{ $slide['title'] }}<em>{{ $slide['accent'] }}</em></h2>
                    @endif
                    <p class="lk-carousel__description">{{ $slide['description'] }}</p>
                    <div class="lk-carousel__actions">
                        <a href="{{ route('products', $slide['category'] ? ['category' => $slide['category']] : []) }}" class="lk-btn lk-btn-red">{{ $slide['cta'] }} <span aria-hidden="true">&rarr;</span></a>
                        <a href="#lk-categories" class="lk-carousel__explore">Explore Categories <span aria-hidden="true">&nearr;</span></a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    <div class="lk-carousel__controls" hidden>
        <div class="lk-carousel__dots" aria-label="Choose a slide">
            @foreach($slides as $slide)
                <button type="button" data-hero-dot="{{ $loop->index }}" aria-label="Show slide {{ $loop->iteration }}: {{ $slide['title'] }} {{ $slide['accent'] }}" aria-controls="hero-slide-{{ $loop->index }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"><span></span></button>
            @endforeach
        </div>
        <span class="lk-carousel__count" aria-hidden="true"><span data-hero-count>01</span> / 03</span>
        <div class="lk-carousel__buttons">
            <button type="button" data-hero-prev aria-label="Previous slide">&larr;</button>
            <button type="button" data-hero-next aria-label="Next slide">&rarr;</button>
            <button type="button" data-hero-pause aria-label="Pause slideshow">Pause</button>
        </div>
    </div>
    <span class="lk-carousel__sr" data-hero-status aria-live="polite" aria-atomic="true"></span>
</section>
