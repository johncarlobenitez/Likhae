<x-marketplace.layout title="Local Finds" :buyer="true">
<section class="lk-catalog-hero lk-container">
    <span class="lk-kicker">FROM AROUND THE PHILIPPINES</span>
    <h1>Find by place.</h1>
    <p>Explore independent makers and heritage craft traditions through the cities and regions they call home.</p>
</section>

<section class="lk-container lk-place-grid">
    @foreach([
        ['Cebu', 'Craft, resort wear, ceramics & shell art', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?auto=format&fit=crop&w=600&q=80'],
        ['Benguet', 'Highland coffee, inabel weaving & mountain pine', 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=600&q=80'],
        ['Marikina', 'Heritage leather footwear & handmade goods', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80'],
        ['Davao', 'Botanical home fragrance, vetiver & cacao', 'https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=600&q=80'],
        ['Manila', 'Modern independent design & gold jewelry', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80']
    ] as $p)
        <a href="{{ route('buyer.products').'?location='.$p[0] }}" style="background-image:linear-gradient(180deg, rgba(238,233,225,0.85) 0%, rgba(238,233,225,0.98) 100%)">
            <span>{{ $p[1] }}</span>
            <strong>{{ $p[0] }}</strong>
            <b>Explore makers →</b>
        </a>
    @endforeach
</section>
</x-marketplace.layout>
