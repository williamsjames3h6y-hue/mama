import { getSettings, updateSetting } from '../../services/database.js';
import { signOut } from '../../services/auth.js';
import { navigate, updateSettingsData } from '../../main.js';

export async function renderAdminSettings(app, user, currentSettings) {
  app.innerHTML = `
    <div class="admin-layout">
      ${renderSidebar()}
      <div class="admin-content">
        <h1 style="margin-bottom: 2rem;">Site Settings</h1>

        <div id="alert"></div>

        <form id="settingsForm">
          <div class="card">
            <h3>Contact Information</h3>
            <div class="form-group">
              <label>Contact Email</label>
              <input type="email" name="contact_email" value="${currentSettings.contact_email || ''}" required>
            </div>
            <div class="form-group">
              <label>Contact Phone</label>
              <input type="text" name="contact_phone" value="${currentSettings.contact_phone || ''}">
            </div>
            <div class="form-group">
              <label>Contact Address</label>
              <textarea name="contact_address" rows="4">${currentSettings.contact_address || ''}</textarea>
            </div>
          </div>

          <div class="card">
            <h3>VIP Level Rates ($/hour)</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
              <div class="form-group">
                <label>VIP 1 Rate</label>
                <input type="number" name="vip1_rate" step="0.01" value="${currentSettings.vip1_rate || 10}" required>
              </div>
              <div class="form-group">
                <label>VIP 2 Rate</label>
                <input type="number" name="vip2_rate" step="0.01" value="${currentSettings.vip2_rate || 20}" required>
              </div>
              <div class="form-group">
                <label>VIP 3 Rate</label>
                <input type="number" name="vip3_rate" step="0.01" value="${currentSettings.vip3_rate || 50}" required>
              </div>
              <div class="form-group">
                <label>VIP 4 Rate</label>
                <input type="number" name="vip4_rate" step="0.01" value="${currentSettings.vip4_rate || 100}" required>
              </div>
              <div class="form-group">
                <label>VIP 5 Rate</label>
                <input type="number" name="vip5_rate" step="0.01" value="${currentSettings.vip5_rate || 200}" required>
              </div>
            </div>
          </div>

          <div class="card">
            <h3>Other Settings</h3>
            <div class="form-group">
              <label>Referral Bonus ($)</label>
              <input type="number" name="referral_bonus" step="0.01" value="${currentSettings.referral_bonus || 300}" required>
            </div>
            <div class="form-group">
              <label>Minimum Withdrawal ($)</label>
              <input type="number" name="min_withdrawal" step="0.01" value="${currentSettings.min_withdrawal || 50}" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.125rem;">Save All Settings</button>
        </form>
      </div>
    </div>
  `;

  document.getElementById('settingsForm').addEventListener('submit', handleSaveSettings);
}

function renderSidebar() {
  return `
    <div class="admin-sidebar">
      <h2 style="margin-bottom: 2rem;">Admin Panel</h2>
      <ul class="admin-nav">
        <li><a href="#/admin" class="admin-nav-link">Dashboard</a></li>
        <li><a href="#/admin/users" class="admin-nav-link">Users</a></li>
        <li><a href="#/admin/projects" class="admin-nav-link">Projects</a></li>
        <li><a href="#/admin/settings" class="admin-nav-link active">Settings</a></li>
        <li><a href="#/" class="admin-nav-link">Back to Site</a></li>
        <li><button class="btn btn-danger" onclick="handleLogout()" style="width: 100%; margin-top: 1rem;">Logout</button></li>
      </ul>
    </div>
  `;
}

async function handleSaveSettings(e) {
  e.preventDefault();
  const alert = document.getElementById('alert');
  const form = e.target;
  const formData = new FormData(form);

  try {
    const updates = {};
    for (const [key, value] of formData.entries()) {
      await updateSetting(key, value);
      updates[key] = value;
    }

    updateSettingsData(updates);
    alert.innerHTML = '<div class="alert alert-success">Settings saved successfully!</div>';
    setTimeout(() => {
      alert.innerHTML = '';
    }, 3000);
  } catch (error) {
    alert.innerHTML = `<div class="alert alert-error">Failed to save settings: ${error.message}</div>`;
  }
}

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
