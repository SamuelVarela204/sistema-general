const sidebar = document.getElementById('sidebar');
const holder = document.getElementById('hoverHolder');
let closeTimeout = null;

function openSidebar() { 
    clearTimeout(closeTimeout); 
    if (sidebar) sidebar.style.transform = 'translateX(0)'; 
}

function closeSidebar() { 
    if (sidebar) sidebar.style.transform = 'translateX(-260px)'; 
}

if (holder && sidebar) {
    holder.addEventListener('mouseenter', openSidebar);
    holder.addEventListener('mouseleave', () => { closeTimeout = setTimeout(closeSidebar, 350); });
    sidebar.addEventListener('mouseenter', () => clearTimeout(closeTimeout));
    sidebar.addEventListener('mouseleave', () => { closeTimeout = setTimeout(closeSidebar, 350); });
    closeSidebar();
}

document.documentElement.style.zoom = '100%';