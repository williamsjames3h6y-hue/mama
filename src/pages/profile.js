import { signOut } from '../services/auth.js';
import { navigate } from '../main.js';

export function renderProfilePage(app, user, settings) {
  app.innerHTML = `
    ${renderNavbar(user)}
    <div class="container">
      <h1 style="margin-bottom: 2rem;">My Profile</h1>

      <div class="card">
        <h3>Personal Information</h3>
        <div style="margin-top: 1.5rem;">
          <p><strong>Name:</strong> ${user.full_name}</p>
          <p><strong>Email:</strong> ${user.email}</p>
          <p><strong>VIP Level:</strong> <span class="badge badge-vip">Level ${user.vip_level}</span></p>
          <p><strong>Role:</strong> <span class="badge badge-${user.role}">${user.role.toUpperCase()}</span></p>
          <p><strong>Member Since:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
        </div>
      </div>

      <div class="card">
        <h3>Financial Summary</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
          <div>
            <p style="color: #95a5a6;">Current Balance</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #3498db;">$${user.balance.toFixed(2)}</p>
          </div>
          <div>
            <p style="color: #95a5a6;">Total Earned</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #2ecc71;">$${user.total_earned.toFixed(2)}</p>
          </div>
        </div>
      </div>

      <div class="card">
        <h3>Referral Program</h3>
        <p style="margin-top: 1rem;">Your Referral Code: <strong style="font-size: 1.25rem; color: #3498db;">${user.referral_code}</strong></p>
        <p style="color: #95a5a6; margin-top: 0.5rem;">Earn $${settings.referral_bonus || '300'} for each person who signs up using your code!</p>
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
