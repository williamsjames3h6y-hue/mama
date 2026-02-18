import { getUsers, getAllProjects, getPayments } from '../../services/database.js';
import { signOut } from '../../services/auth.js';
import { navigate } from '../../main.js';

export async function renderAdminDashboard(app, user) {
  const users = await getUsers();
  const projects = await getAllProjects();
  const payments = await getPayments();

  const totalEarnings = payments
    .filter(p => p.type === 'earning' && p.status === 'completed')
    .reduce((sum, p) => sum + p.amount, 0);

  app.innerHTML = `
    <div class="admin-layout">
      ${renderSidebar()}
      <div class="admin-content">
        <h1 style="margin-bottom: 2rem;">Admin Dashboard</h1>

        <div class="stats-grid">
          <div class="stat-card">
            <div style="color: #95a5a6; margin-bottom: 0.5rem;">Total Users</div>
            <div class="stat-value">${users.length}</div>
          </div>
          <div class="stat-card">
            <div style="color: #95a5a6; margin-bottom: 0.5rem;">Active Projects</div>
            <div class="stat-value">${projects.filter(p => p.status === 'open').length}</div>
          </div>
          <div class="stat-card">
            <div style="color: #95a5a6; margin-bottom: 0.5rem;">Total Projects</div>
            <div class="stat-value">${projects.length}</div>
          </div>
          <div class="stat-card">
            <div style="color: #95a5a6; margin-bottom: 0.5rem;">Total Earnings</div>
            <div class="stat-value">$${totalEarnings.toFixed(2)}</div>
          </div>
        </div>

        <div class="card">
          <h3>Quick Actions</h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
            <button class="btn btn-primary" onclick="window.location.hash='/admin/users'">Manage Users</button>
            <button class="btn btn-primary" onclick="window.location.hash='/admin/projects'">Manage Projects</button>
            <button class="btn btn-primary" onclick="window.location.hash='/admin/settings'">Settings</button>
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderSidebar() {
  return `
    <div class="admin-sidebar">
      <h2 style="margin-bottom: 2rem;">Admin Panel</h2>
      <ul class="admin-nav">
        <li><a href="#/admin" class="admin-nav-link active">Dashboard</a></li>
        <li><a href="#/admin/users" class="admin-nav-link">Users</a></li>
        <li><a href="#/admin/projects" class="admin-nav-link">Projects</a></li>
        <li><a href="#/admin/settings" class="admin-nav-link">Settings</a></li>
        <li><a href="#/" class="admin-nav-link">Back to Site</a></li>
        <li><button class="btn btn-danger" onclick="handleLogout()" style="width: 100%; margin-top: 1rem;">Logout</button></li>
      </ul>
    </div>
  `;
}

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
