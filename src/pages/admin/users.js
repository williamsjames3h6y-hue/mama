import { getUsers, updateUser } from '../../services/database.js';
import { signOut } from '../../services/auth.js';
import { navigate } from '../../main.js';

export async function renderAdminUsers(app, user) {
  const users = await getUsers();

  app.innerHTML = `
    <div class="admin-layout">
      ${renderSidebar()}
      <div class="admin-content">
        <h1 style="margin-bottom: 2rem;">Manage Users</h1>

        <div class="card">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>VIP Level</th>
                <th>Balance</th>
                <th>Total Earned</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              ${users.map(u => `
                <tr>
                  <td>${u.full_name}</td>
                  <td>${u.email}</td>
                  <td><span class="badge badge-${u.role}">${u.role.toUpperCase()}</span></td>
                  <td><span class="badge badge-vip">VIP ${u.vip_level}</span></td>
                  <td>$${u.balance.toFixed(2)}</td>
                  <td>$${u.total_earned.toFixed(2)}</td>
                  <td>
                    <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="showEditUserModal('${u.id}', ${u.vip_level}, '${u.role}')">Edit</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="editUserModal" class="modal">
      <div class="modal-content">
        <h2>Edit User</h2>
        <form id="editUserForm">
          <input type="hidden" id="editUserId">
          <div class="form-group">
            <label>VIP Level</label>
            <select id="editVipLevel">
              <option value="1">VIP 1</option>
              <option value="2">VIP 2</option>
              <option value="3">VIP 3</option>
              <option value="4">VIP 4</option>
              <option value="5">VIP 5</option>
            </select>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select id="editRole">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditUserModal()">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  `;

  document.getElementById('editUserForm').addEventListener('submit', handleEditUser);
}

function renderSidebar() {
  return `
    <div class="admin-sidebar">
      <h2 style="margin-bottom: 2rem;">Admin Panel</h2>
      <ul class="admin-nav">
        <li><a href="#/admin" class="admin-nav-link">Dashboard</a></li>
        <li><a href="#/admin/users" class="admin-nav-link active">Users</a></li>
        <li><a href="#/admin/projects" class="admin-nav-link">Projects</a></li>
        <li><a href="#/admin/settings" class="admin-nav-link">Settings</a></li>
        <li><a href="#/" class="admin-nav-link">Back to Site</a></li>
        <li><button class="btn btn-danger" onclick="handleLogout()" style="width: 100%; margin-top: 1rem;">Logout</button></li>
      </ul>
    </div>
  `;
}

window.showEditUserModal = function(userId, vipLevel, role) {
  document.getElementById('editUserId').value = userId;
  document.getElementById('editVipLevel').value = vipLevel;
  document.getElementById('editRole').value = role;
  document.getElementById('editUserModal').classList.add('active');
};

window.closeEditUserModal = function() {
  document.getElementById('editUserModal').classList.remove('active');
};

async function handleEditUser(e) {
  e.preventDefault();
  const userId = document.getElementById('editUserId').value;
  const vipLevel = parseInt(document.getElementById('editVipLevel').value);
  const role = document.getElementById('editRole').value;

  try {
    await updateUser(userId, { vip_level: vipLevel, role });
    alert('User updated successfully!');
    closeEditUserModal();
    navigate('/admin/users');
  } catch (error) {
    alert('Failed to update user: ' + error.message);
  }
}

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
