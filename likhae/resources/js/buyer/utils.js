export const $=(s,c=document)=>c.querySelector(s);
export const $$=(s,c=document)=>[...c.querySelectorAll(s)];
export const money=v=>"₱"+Number(v||0).toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2});
export function readStore(k,f=[]){try{return JSON.parse(localStorage.getItem(k))??f}catch{return f}}
export function writeStore(k,v){localStorage.setItem(k,JSON.stringify(v))}
export function toast(m){let w=$("#buyerToastWrap");if(!w){w=document.createElement("div");w.id="buyerToastWrap";w.className="b-toast-wrap";document.body.appendChild(w)}const e=document.createElement("div");e.className="b-toast";e.textContent=m;w.appendChild(e);setTimeout(()=>e.remove(),2800)}
export function escapeHtml(t){const d=document.createElement("div");d.textContent=String(t??"");return d.innerHTML}
