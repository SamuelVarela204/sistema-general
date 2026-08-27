import './bootstrap';

const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const sidebarEdge = document.getElementById('sidebarEdge');
const notificationBell = document.getElementById('notificationBell');
const notificationPanel = document.getElementById('notificationPanel');
const notificationClose = document.getElementById('notificationClose');
const notificationCount = document.getElementById('notificationCount');
const notificationList = document.getElementById('notificationList');

function setSidebar(open) {
	sidebar?.classList.toggle('open', open);
	sidebarOverlay?.classList.toggle('visible', open);
	sidebarToggle?.setAttribute('aria-expanded', String(open));
}

sidebarToggle?.addEventListener('click', () => setSidebar(!sidebar?.classList.contains('open')));
sidebarOverlay?.addEventListener('click', () => setSidebar(false));
let sidebarCloseTimer;
const openSidebar = () => { clearTimeout(sidebarCloseTimer); setSidebar(true); };
const closeSidebarSoon = () => { clearTimeout(sidebarCloseTimer); sidebarCloseTimer = setTimeout(() => setSidebar(false), 280); };
sidebarEdge?.addEventListener('mouseenter', openSidebar);
sidebarEdge?.addEventListener('mouseleave', closeSidebarSoon);
sidebar?.addEventListener('mouseenter', openSidebar);
sidebar?.addEventListener('mouseleave', closeSidebarSoon);
document.addEventListener('keydown', (event) => {
	if (event.key === 'Escape') {
		setSidebar(false);
		notificationPanel?.classList.remove('visible');
	}
});

function updateNotifications() {
	if (!notificationCount || !notificationList) return;
	let items = [];
	try { items = JSON.parse(localStorage.getItem('systemNotifications') || '[]'); } catch { items = []; }
	if (!Array.isArray(items)) items = [];
	notificationCount.textContent = String(items.length);
	notificationCount.hidden = items.length === 0;
	notificationList.replaceChildren();
	if (items.length === 0) notificationList.textContent = 'No hay notificaciones nuevas.';
	items.forEach((item) => {
		const element = document.createElement('p');
		element.textContent = item;
		notificationList.appendChild(element);
	});
}

notificationBell?.addEventListener('click', () => notificationPanel?.classList.toggle('visible'));
notificationClose?.addEventListener('click', () => notificationPanel?.classList.remove('visible'));
updateNotifications();

document.querySelectorAll('.setting-toggle').forEach((toggle) => {
	const key = `taf_${toggle.dataset.setting}`;
	toggle.checked = localStorage.getItem(key) === 'true';
	toggle.addEventListener('change', () => {
		localStorage.setItem(key, String(toggle.checked));
		document.documentElement.classList.toggle(toggle.dataset.setting, toggle.checked);
	});
	document.documentElement.classList.toggle(toggle.dataset.setting, toggle.checked);
});
