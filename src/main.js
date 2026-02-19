import { getCurrentUser, onAuthStateChange } from './services/auth.js';
import { getSettings } from './services/database.js';
import { renderLandingPage } from './pages/landing.js';
import { renderLoginPage } from './pages/login.js';
import { renderRegisterPage } from './pages/register.js';
import { renderDashboard } from './pages/dashboard.js';
import { renderProjectsPage } from './pages/projects.js';
import { renderProfilePage } from './pages/profile.js';
import { renderPaymentsPage } from './pages/payments.js';
import { renderAdminDashboard } from './pages/admin/dashboard.js';
import { renderAdminUsers } from './pages/admin/users.js';
import { renderAdminProjects } from './pages/admin/projects.js';
import { renderAdminSettings } from './pages/admin/settings.js';

let currentUser = null;
let settings = {};

async function init() {
  try {
    currentUser = await getCurrentUser();
  } catch (e) {
    currentUser = null;
  }

  try {
    settings = await getSettings();
  } catch (e) {
    settings = {};
  }

  onAuthStateChange(async (event, user) => {
    currentUser = user;
    router();
  });

  window.addEventListener('popstate', router);
  window.addEventListener('hashchange', router);

  router();
}

export function router() {
  const path = window.location.hash.slice(1) || '/';
  const app = document.getElementById('app');

  if (path === '/login') {
    renderLoginPage(app);
  } else if (path === '/register') {
    renderRegisterPage(app);
  } else if (path === '/') {
    if (currentUser) {
      renderDashboard(app, currentUser, settings);
    } else {
      renderLandingPage(app, settings);
    }
  } else if (path === '/projects') {
    if (!currentUser) {
      navigate('/login');
      return;
    }
    renderProjectsPage(app, currentUser);
  } else if (path === '/profile') {
    if (!currentUser) {
      navigate('/login');
      return;
    }
    renderProfilePage(app, currentUser, settings);
  } else if (path === '/payments') {
    if (!currentUser) {
      navigate('/login');
      return;
    }
    renderPaymentsPage(app, currentUser);
  } else if (path === '/admin') {
    if (!currentUser || currentUser.role !== 'admin') {
      navigate('/login');
      return;
    }
    renderAdminDashboard(app, currentUser);
  } else if (path === '/admin/users') {
    if (!currentUser || currentUser.role !== 'admin') {
      navigate('/login');
      return;
    }
    renderAdminUsers(app, currentUser);
  } else if (path === '/admin/projects') {
    if (!currentUser || currentUser.role !== 'admin') {
      navigate('/login');
      return;
    }
    renderAdminProjects(app, currentUser);
  } else if (path === '/admin/settings') {
    if (!currentUser || currentUser.role !== 'admin') {
      navigate('/login');
      return;
    }
    renderAdminSettings(app, currentUser, settings);
  } else {
    app.innerHTML = '<div class="container"><h1>404 - Page Not Found</h1></div>';
  }
}

export function navigate(path) {
  window.location.hash = path;
}

export function getCurrentUserData() {
  return currentUser;
}

export function getSettingsData() {
  return settings;
}

export function updateSettingsData(newSettings) {
  settings = { ...settings, ...newSettings };
}

init();
