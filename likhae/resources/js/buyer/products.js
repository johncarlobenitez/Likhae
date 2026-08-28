import{$,$$,readStore,writeStore,toast}from"./utils.js";
export function initBuyerProducts(){
 $$("[data-buyer-wishlist-toggle]").forEach(btn=>{
  const id=btn.dataset.productId;btn.classList.toggle("is-active",readStore("likhae_buyer_wishlist",[]).includes(id));
  btn.addEventListener("click",e=>{e.preventDefault();let w=readStore("likhae_buyer_wishlist",[]);if(w.includes(id)){w=w.filter(x=>x!==id);btn.classList.remove("is-active");toast("Removed from wishlist.")}else{w.push(id);btn.classList.add("is-active");toast("Saved to wishlist.")}writeStore("likhae_buyer_wishlist",w)})
 });
 const p=$("[data-buyer-products-page]");if(!p)return;
 const cards=$$("[data-buyer-product-card]",p),cats=$$("[data-buyer-category-filter]",p),search=$("[data-buyer-product-filter-search]",p),sort=$("[data-buyer-sort]",p);
 const filter=()=>{const q=(search?.value||"").trim().toLowerCase(),cat=cats.find(x=>x.checked)?.value||"";cards.forEach(c=>c.hidden=!((!q||(c.dataset.search||"").includes(q))&&(!cat||c.dataset.category===cat)))};
 search?.addEventListener("input",filter);cats.forEach(x=>x.addEventListener("change",filter));
 sort?.addEventListener("change",()=>{const g=$("[data-buyer-product-grid]",p);if(!g)return;[...cards].sort((a,b)=>{const pa=+a.dataset.price,pb=+b.dataset.price,sa=+a.dataset.sold,sb=+b.dataset.sold,ra=+a.dataset.rating,rb=+b.dataset.rating;if(sort.value==="price-low")return pa-pb;if(sort.value==="price-high")return pb-pa;if(sort.value==="best-selling")return sb-sa;if(sort.value==="highest-rated")return rb-ra;return 0}).forEach(c=>g.appendChild(c))});
 $("[data-buyer-filter-toggle]",p)?.addEventListener("click",()=> $("[data-buyer-filter-panel]",p)?.classList.toggle("is-open"));
}
