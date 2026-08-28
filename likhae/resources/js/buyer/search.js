import{$}from"./utils.js";
const data=[
 ["Premium Wireless Headphones","Electronics","wireless-headphones"],
 ["Classic Everyday Backpack","Bags","classic-backpack"],
 ["Lightweight Running Shoes","Sports & Outdoors","running-shoes"],
 ["Everyday Smart Watch","Electronics","smart-watch"],
 ["Daily Skincare Essentials Set","Beauty & Health","skincare-set"],
 ["Compact Mechanical Keyboard","Electronics","mechanical-keyboard"],
];
export function initBuyerSearch(){
 const i=$("[data-buyer-search]"),b=$("[data-buyer-search-suggestions]");if(!i||!b)return;
 const render=()=>{const q=i.value.trim().toLowerCase();if(!q){b.innerHTML="";b.classList.remove("is-open");return}
 const rows=data.filter(x=>x.join(" ").toLowerCase().includes(q)).slice(0,5);
 b.innerHTML=rows.length?rows.map(x=>`<a class="b-search-suggestion" href="/buyer/products/${encodeURIComponent(x[2])}"><span class="b-search-suggestion-icon">⌕</span><span><strong>${x[0]}</strong><small>${x[1]}</small></span></a>`).join(""):`<div style="padding:13px;color:#6F706F;font-size:13px">No matching suggestions.</div>`;
 b.classList.add("is-open")};
 i.addEventListener("input",render);i.addEventListener("focus",render);
 document.addEventListener("click",e=>{if(!b.contains(e.target)&&e.target!==i)b.classList.remove("is-open")});
}
