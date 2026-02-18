import { signOut } from '../services/auth.js';
import { navigate } from '../main.js';
import { getUserProjects, getPayments } from '../services/database.js';

export async function renderDashboard(app, user, settings) {
  const userProjects = await getUserProjects(user.id);
  const payments = await getPayments(user.id);

  const approved = userProjects.filter(p => p.status === 'approved').length;
  const pending = userProjects.filter(p => p.status === 'submitted').length;

  app.innerHTML = `
    ${renderNavbar(user)}
    <div class="container dashboard-page">
      <div style="position: relative; margin-bottom: 2rem;">
        <img src="/assets/images/4.jpg" alt="Dashboard" style="width: 100%; height: 300px; object-fit: cover; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
        <div style="position: absolute; bottom: 2rem; left: 2rem; background: rgba(255,255,255,0.95); padding: 1.5rem 2rem; border-radius: 12px; backdrop-filter: blur(10px);">
          <h1 style="margin-bottom: 0.5rem;">Welcome, ${user.full_name}</h1>
          <span class="badge badge-vip" style="font-size: 1rem; padding: 0.5rem 1rem;">VIP Level ${user.vip_level}</span>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div style="color: #95a5a6; margin-bottom: 0.5rem;">Balance</div>
          <div class="stat-value">$${user.balance.toFixed(2)}</div>
        </div>
        <div class="stat-card">
          <div style="color: #95a5a6; margin-bottom: 0.5rem;">Total Earned</div>
          <div class="stat-value">$${user.total_earned.toFixed(2)}</div>
        </div>
        <div class="stat-card">
          <div style="color: #95a5a6; margin-bottom: 0.5rem;">Approved Projects</div>
          <div class="stat-value">${approved}</div>
        </div>
        <div class="stat-card">
          <div style="color: #95a5a6; margin-bottom: 0.5rem;">Pending Projects</div>
          <div class="stat-value">${pending}</div>
        </div>
      </div>

      <div class="card">
        <h3>Quick Actions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
          <button class="btn btn-primary" onclick="window.location.hash='/projects'">Browse Projects</button>
          <button class="btn btn-secondary" onclick="window.location.hash='/profile'">View Profile</button>
          <button class="btn btn-outline" style="color: #3498db; border-color: #3498db;" onclick="window.location.hash='/payments'">Payment History</button>
        </div>
      </div>

      <div class="card">
        <h3>Your Referral Code</h3>
        <p style="font-size: 1.25rem; font-weight: 700; margin: 1rem 0;">${user.referral_code}</p>
        <p style="color: #95a5a6;">Share this code and earn ${settings.referral_bonus || '300'} USD when someone joins!</p>
      </div>
    </div>
  `;
}

function renderNavbar(user) {
  return `
    <nav class="navbar">
      <div class="navbar-content">
        <a href="#/" class="navbar-brand">EarningsLLC</a>
        <ul class="navbar-menu">
          <li><a href="#/" class="navbar-link">Dashboard</a></li>
          <li><a href="#/projects" class="navbar-link">Projects</a></li>
          <li><a href="#/payments" class="navbar-link">Payments</a></li>
          <li><a href="#/profile" class="navbar-link">Profile</a></li>
          ${user.role === 'admin' ? '<li><a href="#/admin" class="navbar-link" style="color: #e74c3c;">Admin</a></li>' : ''}
          <li><button class="btn btn-primary" onclick="handleLogout()">Logout</button></li>
        </ul>
      </div>
    </nav>
  `;
}

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
