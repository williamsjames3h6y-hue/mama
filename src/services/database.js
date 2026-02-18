import { apiCall } from '../config/api.js';

export async function initializeDatabase() {
  try {
    await apiCall('/users');
    console.log('Database connection verified');
    return true;
  } catch (error) {
    console.log('Database needs initialization');
    return false;
  }
}

export async function getUsers() {
  return await apiCall('/users');
}

export async function getUserById(id) {
  return await apiCall(`/users/${id}`);
}

export async function createUser(userData) {
  return await apiCall('/users', {
    method: 'POST',
    body: JSON.stringify(userData),
  });
}

export async function updateUser(id, updates) {
  return await apiCall(`/users/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates),
  });
}

export async function getProjects(vipLevel = 1) {
  return await apiCall(`/projects?vipLevel=${vipLevel}`);
}

export async function getAllProjects() {
  return await apiCall('/projects/all');
}

export async function createProject(projectData) {
  return await apiCall('/projects', {
    method: 'POST',
    body: JSON.stringify(projectData),
  });
}

export async function updateProject(id, updates) {
  return await apiCall(`/projects/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates),
  });
}

export async function deleteProject(id) {
  return await apiCall(`/projects/${id}`, {
    method: 'DELETE',
  });
}

export async function getSettings() {
  return await apiCall('/settings');
}

export async function updateSetting(key, value) {
  return await apiCall(`/settings/${key}`, {
    method: 'PUT',
    body: JSON.stringify({ value }),
  });
}

export async function getUserProjects(userId) {
  return await apiCall(`/user-projects/${userId}`);
}

export async function applyToProject(userId, projectId) {
  return await apiCall('/user-projects', {
    method: 'POST',
    body: JSON.stringify({ userId, projectId }),
  });
}

export async function getPayments(userId = null) {
  const query = userId ? `?userId=${userId}` : '';
  return await apiCall(`/payments${query}`);
}
