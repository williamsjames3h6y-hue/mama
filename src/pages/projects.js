import { getProjects, applyToProject, getUserProjects } from '../services/database.js';
import { navigate } from '../main.js';
import { signOut } from '../services/auth.js';

export async function renderProjectsPage(app, user) {
  const projects = await getProjects(user.vip_level);
  const userProjects = await getUserProjects(user.id);
  const appliedIds = new Set(userProjects.map(p => p.project_id));

  app.innerHTML = `
    ${renderNavbar(user)}
    <div class="container projects-page">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1>Available Projects</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
          <img src="/assets/images/5.jpg" alt="Projects" style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
          <img src="/assets/images/6.jpg" alt="Work" style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        </div>
      </div>
      <div class="projects-grid">
        ${projects.length === 0 ? '<div class="card"><p>No projects available at your VIP level.</p></div>' : ''}
        ${projects.map(project => `
          <div class="project-card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
              <h3>${project.title}</h3>
              <span class="badge badge-vip">VIP ${project.vip_level_required}</span>
            </div>
            <p style="color: #95a5a6; margin-bottom: 1rem;">${project.description}</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="color: #3498db; font-size: 1.125rem;">$${project.rate_min} - $${project.rate_max}/hr</strong>
                <span style="color: #95a5a6; margin-left: 1rem;">${project.project_type}</span>
              </div>
              ${appliedIds.has(project.id)
                ? '<span class="badge badge-vip">Applied</span>'
                : `<button class="btn btn-primary" onclick="applyToProject('${project.id}')">Apply Now</button>`
              }
            </div>
          </div>
        `).join('')}
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

window.applyToProject = async function(projectId) {
  const { getCurrentUserData } = await import('../main.js');
  const user = getCurrentUserData();
  if (!user) return;

  const hoursInput = prompt('Enter estimated hours to work on this project:');
  if (!hoursInput) return;

  const hours = parseFloat(hoursInput);
  if (isNaN(hours) || hours <= 0) {
    alert('Please enter a valid number of hours');
    return;
  }

  try {
    await applyToProject(user.id, projectId, hours);
    alert('Application submitted successfully!');
    navigate('/projects');
  } catch (error) {
    alert('Failed to apply: ' + error.message);
  }
};

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
