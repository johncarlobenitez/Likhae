import{$,readStore}from"./utils.js";
export function initBuyerHeader(){
 const t=$("[data-buyer-category-toggle]"),m=$("[data-buyer-category-menu]");
 t?.addEventListener("click",e=>{e.stopPropagation();m?.classList.toggle("is-open")});
 document.addEventListener("click",e=>{if(m&&!m.contains(e.target)&&e.target!==t)m.classList.remove("is-open")});
 const mt=$("[data-buyer-mobile-toggle]"),mn=$("[data-buyer-mobile-nav]");
 mt?.addEventListener("click",()=>mn?.classList.toggle("is-open"));
 const w=$("[data-buyer-wishlist-count]"),c=$("[data-buyer-cart-count]");
 if(w)w.textContent=readStore("likhae_buyer_wishlist",[]).length;
 if(c)c.textContent=readStore("likhae_buyer_cart",[]).reduce((a,i)=>a+Number(i.qty||1),0);
}
