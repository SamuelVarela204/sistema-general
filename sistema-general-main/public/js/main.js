const loadingIndicator = document.createElement('div');
loadingIndicator.className = 'loading-indicator';
loadingIndicator.setAttribute('role', 'status');
loadingIndicator.setAttribute('aria-live', 'polite');
loadingIndicator.setAttribute('aria-label', 'Cargando');
loadingIndicator.innerHTML = '<span class="loading-spinner" aria-hidden="true"></span>';
document.body.appendChild(loadingIndicator);

function mostrarCarga() {
    loadingIndicator.classList.add('is-visible');
}

function ocultarCarga() {
    loadingIndicator.classList.remove('is-visible');
}

window.addEventListener('load', ocultarCarga, { once: true });

const fetchOriginal = window.fetch;
window.fetch = (...args) => {
    mostrarCarga();
    return fetchOriginal(...args).finally(ocultarCarga);
};

document.addEventListener('submit', (event) => {
    if (event.defaultPrevented) return;
    mostrarCarga();
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link || event.defaultPrevented || link.target === '_blank' || link.hasAttribute('download')) return;

    const url = new URL(link.href, window.location.href);
    if (url.origin === window.location.origin && url.href !== window.location.href && !url.hash) {
        mostrarCarga();
    }
});

const sidebar = document.getElementById("sidebar");
const holder = document.getElementById("hoverHolder");
let closeTimeout = null;

function openSidebar() {
    clearTimeout(closeTimeout);
    if (sidebar) sidebar.style.transform = "translateX(0)";
}

function closeSidebar() {
    if (sidebar) sidebar.style.transform = "translateX(-260px)";
}

if (holder && sidebar) {
    holder.addEventListener("mouseenter", openSidebar);
    holder.addEventListener("mouseleave", () => {
        closeTimeout = setTimeout(closeSidebar, 350);
    });
    sidebar.addEventListener("mouseenter", () => clearTimeout(closeTimeout));
    sidebar.addEventListener("mouseleave", () => {
        closeTimeout = setTimeout(closeSidebar, 350);
    });
    closeSidebar();
}

const notificationBell = document.getElementById('notificationBell');
const notificationPanel = document.getElementById('notificationPanel');
const notificationClose = document.getElementById('notificationClose');
const notificationWidget = document.getElementById('notificationWidget');
const notificationCount = document.getElementById('notificationCount');
const notificationList = document.getElementById('notificationList');

function setNotificationPanelVisible(visible) {
    if (!notificationPanel) return;
    notificationPanel.classList.toggle('visible', visible);
    notificationPanel.classList.toggle('hidden', !visible);
    notificationPanel.setAttribute('aria-hidden', visible ? 'false' : 'true');
}

function updateNotifications() {
    if (!notificationCount || !notificationList) return;
    const stored = localStorage.getItem('systemNotifications');
    let items = [];
    try {
        items = stored ? JSON.parse(stored) : [];
    } catch (e) {
        items = [];
    }
    if (!Array.isArray(items)) items = [];
    notificationList.innerHTML = '';
    if (items.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'notification-item empty';
        empty.textContent = 'No hay notificaciones nuevas.';
        notificationList.appendChild(empty);
    } else {
        items.forEach((item) => {
            const div = document.createElement('div');
            div.className = 'notification-item';
            div.textContent = item;
            notificationList.appendChild(div);
        });
    }
    notificationCount.textContent = String(items.length);
    notificationCount.style.display = items.length > 0 ? 'inline-flex' : 'none';
}

function toggleNotificationPanel() {
    if (!notificationPanel) return;
    setNotificationPanelVisible(!notificationPanel.classList.contains('visible'));
}

if (notificationBell) {
    notificationBell.addEventListener('click', (event) => {
        event.stopPropagation();
        toggleNotificationPanel();
    });
}
if (notificationClose) {
    notificationClose.addEventListener('click', () => {
        setNotificationPanelVisible(false);
    });
}

window.addEventListener('click', (event) => {
    if (!notificationWidget || !notificationPanel || !notificationPanel.classList.contains('visible')) return;
    if (!notificationWidget.contains(event.target)) {
        setNotificationPanelVisible(false);
    }
});

window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setNotificationPanelVisible(false);
    }
});

updateNotifications();

document.documentElement.style.zoom = "100%";
