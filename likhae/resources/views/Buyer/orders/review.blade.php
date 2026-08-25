@props(['id' => 'LKH-2026-0810-0921'])
<x-marketplace.layout title="Rate Product" :buyer="true">
<section class="lk-page-head lk-container">
    <span class="lk-kicker">YOUR FEEDBACK</span>
    <h1>Rate your purchase</h1>
    <p>Help other buyers and support the maker by sharing your experience with the craftsmanship and fit.</p>
</section>

<section class="lk-narrow lk-container">
    <form class="lk-checkout-card" data-review-form>
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--line)">
            <img src="https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=150&q=80" alt="Sienna Stoneware Mug" style="width:70px;height:70px;border-radius:8px;object-fit:cover">
            <div>
                <h2 style="font-size:1.4rem;margin:0">Sienna Stoneware Mug</h2>
                <span style="color:var(--slate);font-size:0.85rem">Clay Story · Quezon City</span>
            </div>
        </div>

        <label>Overall Product Rating *
            <div class="lk-star-picker" data-star-picker style="margin-top:6px">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" data-star="{{ $i }}" aria-label="Rate {{ $i }} stars">★</button>
                @endfor
            </div>
        </label>

        <label style="margin-top:16px">How was the product quality?
            <select name="quality" style="margin-top:6px">
                <option value="Exceptional">Exceptional — Exceeded expectations</option>
                <option value="Great">Great — True to photos and description</option>
                <option value="Average">Average — Acceptable</option>
            </select>
        </label>

        <label style="margin-top:16px">Your Written Review *
            <textarea rows="5" required placeholder="What did you love? How was the texture, finish, packaging, and handling?" style="margin-top:6px"></textarea>
        </label>

        <label style="margin-top:16px">Add Photos (Optional)
            <input type="file" accept="image/*" multiple style="margin-top:6px">
            <small style="color:var(--slate);font-size:0.75rem">Upload up to 3 photos of the received item.</small>
        </label>

        <div style="display:flex;gap:12px;align-items:center;margin-top:24px">
            <button class="lk-btn lk-btn--primary" type="submit">Submit Review</button>
            <a class="lk-btn lk-btn--ghost" href="{{ route('buyer.orders') }}">Cancel</a>
        </div>
    </form>
</section>
</x-marketplace.layout>
