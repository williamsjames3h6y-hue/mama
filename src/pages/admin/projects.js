import { getAllProjects, createProject, updateProject, deleteProject } from '../../services/database.js';
import { signOut } from '../../services/auth.js';
import { navigate } from '../../main.js';

export async function renderAdminProjects(app, user) {
  const projects = await getAllProjects();

  app.innerHTML = `
    <div class="admin-layout">
      ${renderSidebar()}
      <div class="admin-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
          <h1>Manage Projects</h1>
          <button class="btn btn-primary" onclick="showAddProjectModal()">Add New Project</button>
        </div>

        <div class="card">
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>Rate Range</th>
                <th>VIP Level</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              ${projects.map(p => `
                <tr>
                  <td>${p.title}</td>
                  <td>$${p.rate_min} - $${p.rate_max}/hr</td>
                  <td><span class="badge badge-vip">VIP ${p.vip_level_required}</span></td>
                  <td>${p.project_type}</td>
                  <td><span class="badge badge-${p.status}">${p.status.toUpperCase()}</span></td>
                  <td>
                    <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="toggleProjectStatus('${p.id}', '${p.status}')">
                      ${p.status === 'open' ? 'Close' : 'Open'}
                    </button>
                    <button class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="deleteProjectById('${p.id}')">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="addProjectModal" class="modal">
      <div class="modal-content">
        <h2>Add New Project</h2>
        <form id="addProjectForm">
          <div class="form-group">
            <label>Title</label>
            <input type="text" id="title" required>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea id="description" rows="4" required></textarea>
          </div>
          <div class="form-group">
            <label>Min Rate ($/hr)</label>
            <input type="number" id="rate_min" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Max Rate ($/hr)</label>
            <input type="number" id="rate_max" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Project Type</label>
            <input type="text" id="project_type" value="Remote" required>
          </div>
          <div class="form-group">
            <label>VIP Level Required</label>
            <select id="vip_level_required">
              <option value="1">VIP 1</option>
              <option value="2">VIP 2</option>
              <option value="3">VIP 3</option>
              <option value="4">VIP 4</option>
              <option value="5">VIP 5</option>
            </select>
          </div>
          <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Create Project</button>
            <button type="button" class="btn btn-secondary" onclick="closeAddProjectModal()">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  `;

  document.getElementById('addProjectForm').addEventListener('submit', handleAddProject);
}

function renderSidebar() {
  return `
    <div class="admin-sidebar">
      <h2 style="margin-bottom: 2rem;">Admin Panel</h2>
      <ul class="admin-nav">
        <li><a href="#/admin" class="admin-nav-link">Dashboard</a></li>
        <li><a href="#/admin/users" class="admin-nav-link">Users</a></li>
        <li><a href="#/admin/projects" class="admin-nav-link active">Projects</a></li>
        <li><a href="#/admin/settings" class="admin-nav-link">Settings</a></li>
        <li><a href="#/" class="admin-nav-link">Back to Site</a></li>
        <li><button class="btn btn-danger" onclick="handleLogout()" style="width: 100%; margin-top: 1rem;">Logout</button></li>
      </ul>
    </div>
  `;
}

window.showAddProjectModal = function() {
  document.getElementById('addProjectModal').classList.add('active');
};

window.closeAddProjectModal = function() {
  document.getElementById('addProjectModal').classList.remove('active');
};

async function handleAddProject(e) {
  e.preventDefault();
  const form = e.target;

  try {
    await createProject({
      title: form.title.value,
      description: form.description.value,
      rate_min: parseFloat(form.rate_min.value),
      rate_max: parseFloat(form.rate_max.value),
      project_type: form.project_type.value,
      vip_level_required: parseInt(form.vip_level_required.value),
      status: 'open'
    });
    alert('Project created successfully!');
    closeAddProjectModal();
    navigate('/admin/projects');
  } catch (error) {
    alert('Failed to create project: ' + error.message);
  }
}

window.toggleProjectStatus = async function(projectId, currentStatus) {
  const newStatus = currentStatus === 'open' ? 'closed' : 'open';
  try {
    await updateProject(projectId, { status: newStatus });
    navigate('/admin/projects');
  } catch (error) {
    alert('Failed to update project: ' + error.message);
  }
};

window.deleteProjectById = async function(projectId) {
  if (!confirm('Are you sure you want to delete this project?')) return;

  try {
    await deleteProject(projectId);
    alert('Project deleted successfully!');
    navigate('/admin/projects');
  } catch (error) {
    alert('Failed to delete project: ' + error.message);
  }
};

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
