const initSeller = () => {
    const sidebar = document.getElementById('sellerSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const open = document.getElementById('sidebarOpen');
    const close = document.getElementById('sidebarClose');

    const openSidebar = () => {
        sidebar?.classList.add('is-open');
        overlay?.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    };

    const closeSidebar = () => {
        sidebar?.classList.remove('is-open');
        overlay?.classList.remove('is-open');
        document.body.style.overflow = '';
    };

    open?.addEventListener('click', openSidebar);
    close?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    document.querySelectorAll('[data-modal-open]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.modalOpen)?.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.modalClose)?.classList.add('hidden');
        });
    });
};

// Run on initial load and after every Turbo navigation
document.addEventListener('DOMContentLoaded', initSeller);
document.addEventListener('turbo:load', initSeller);
