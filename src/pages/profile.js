import { signOut } from '../services/auth.js';
import { navigate } from '../main.js';

export function renderProfilePage(app, user, settings) {
  const vipLevelNames = ['', 'Beginner', 'Intermediate', 'Advanced', 'Expert', 'Master'];
  const vipRates = ['', settings.vip1_rate || '10', settings.vip2_rate || '20', settings.vip3_rate || '50', settings.vip4_rate || '100', settings.vip5_rate || '200'];

  app.innerHTML = `
    ${renderNavbar(user)}
    <div class="container profile-page">
      <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem; flex-wrap: wrap; gap: 2rem;">
        <div>
          <h1 style="margin-bottom: 1rem;">My Profile</h1>
          <div class="vip-level-indicator">
            <div class="vip-icon">
              <svg width="80" height="80" viewBox="0 0 80 80" style="filter: drop-shadow(0 4px 8px rgba(255, 215, 0, 0.3));">
                <polygon points="40,10 50,30 72,35 56,50 60,72 40,62 20,72 24,50 8,35 30,30" fill="url(#vipGradient)"/>
                <defs>
                  <linearGradient id="vipGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#ffd700;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#ffa500;stop-opacity:1" />
                  </linearGradient>
                </defs>
                <text x="40" y="50" text-anchor="middle" font-size="24" font-weight="bold" fill="#fff">${user.vip_level}</text>
              </svg>
            </div>
            <div>
              <div style="font-size: 1.75rem; font-weight: 700; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">VIP Level ${user.vip_level}</div>
              <div style="font-size: 1.125rem; color: #95a5a6; margin-top: 0.25rem;">${vipLevelNames[user.vip_level]}</div>
              <div style="font-size: 1rem; color: #3498db; margin-top: 0.5rem; font-weight: 600;">$${vipRates[user.vip_level]}/hour rate</div>
            </div>
          </div>
        </div>
        <img src="/assets/images/8.jpg" alt="Profile" style="width: 300px; height: 200px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
      </div>

      <div class="card">
        <h3>Personal Information</h3>
        <div style="margin-top: 1.5rem;">
          <p><strong>Name:</strong> ${user.full_name}</p>
          <p><strong>Email:</strong> ${user.email}</p>
          <p><strong>Current Level:</strong> <span class="badge badge-vip">VIP ${user.vip_level} - ${vipLevelNames[user.vip_level]}</span></p>
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
