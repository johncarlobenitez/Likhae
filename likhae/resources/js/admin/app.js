const qs=(s,c=document)=>c.querySelector(s);
const qsa=(s,c=document)=>[...c.querySelectorAll(s)];

function toast(message){
    let wrap=qs("#lkToastWrap");
    if(!wrap){
        wrap=document.createElement("div");
        wrap.id="lkToastWrap";
        wrap.className="lk-toast-wrap";
        document.body.appendChild(wrap);
    }
    const el=document.createElement("div");
    el.className="lk-toast";
    el.textContent=message;
    wrap.appendChild(el);
    setTimeout(()=>el.remove(),2600);
}

function initShell(){
    const sidebar=qs("[data-sidebar]");
    qs("[data-sidebar-toggle]")?.addEventListener("click",()=>sidebar?.classList.toggle("is-open"));
    document.addEventListener("click",e=>{
        if(window.innerWidth<=860 && sidebar?.classList.contains("is-open") && !sidebar.contains(e.target) && !e.target.closest("[data-sidebar-toggle]")){
            sidebar.classList.remove("is-open");
        }
    });

    qs("[data-mark-notifications]")?.addEventListener("click",()=>{
        qsa("[data-notification-dot]").forEach(x=>x.remove());
        toast("Notifications marked as read.");
    });

    qsa("[data-open-modal]").forEach(btn=>btn.addEventListener("click",()=>{
        const m=document.getElementById(btn.dataset.openModal);
        if(m)m.hidden=false;
    }));
    qsa("[data-close-modal]").forEach(btn=>btn.addEventListener("click",()=>{
        btn.closest(".lk-modal").hidden=true;
    }));

    qsa("[data-tab]").forEach(btn=>btn.addEventListener("click",()=>{
        const parent=btn.closest("[data-tab-group]") || document;
        qsa("[data-tab]",parent).forEach(x=>x.classList.remove("is-active"));
        btn.classList.add("is-active");
        const target=btn.dataset.tab;
        qsa("[data-tab-panel]",parent).forEach(p=>p.hidden=p.dataset.tabPanel!==target);
    }));

    const search=qs("[data-table-search]");
    if(search){
        search.addEventListener("input",()=>{
            const q=search.value.toLowerCase().trim();
            qsa("[data-search-row]").forEach(row=>{
                row.hidden=!row.innerText.toLowerCase().includes(q);
            });
        });
    }

    qsa("[data-confirm-action]").forEach(btn=>btn.addEventListener("click",()=>{
        const action=btn.dataset.confirmAction || "Action";
        const subject=btn.dataset.subject || "item";
        if(confirm(`${action} ${subject}?`)){
            toast(`${action} completed for ${subject}.`);
            const row=btn.closest("[data-search-row]");
            if(row && ["Archive","Deactivate","Reject"].includes(action)) row.style.opacity=".48";
        }
    }));

    qsa("[data-frontend-action]").forEach(btn=>btn.addEventListener("click",()=>{
        toast(btn.dataset.frontendAction || "Action completed.");
    }));

    qs("[data-export-csv]")?.addEventListener("click",()=>{
        const table=qs(".lk-table");
        if(!table){toast("No table data to export.");return}
        const rows=qsa("tr",table).map(tr=>qsa("th,td",tr).map(td=>`"${td.innerText.replaceAll('"','""')}"`).join(",")).join("\n");
        const blob=new Blob([rows],{type:"text/csv"});
        const url=URL.createObjectURL(blob);
        const a=document.createElement("a");
        a.href=url;a.download=`likhae-report-${new Date().toISOString().slice(0,10)}.csv`;a.click();
        URL.revokeObjectURL(url);
        toast("CSV report generated.");
    });

    qsa("[data-print]").forEach(btn=>btn.addEventListener("click",()=>{
        toast("Opening print dialog.");
        window.print();
    }));
}

document.addEventListener("DOMContentLoaded",initShell);
