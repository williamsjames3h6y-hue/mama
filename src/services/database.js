import { apiCall } from '../config/api.js';

export async function initializeDatabase() {
  try {
    await apiCall('/health');
    console.log('Database connection verified');
    return true;
  } catch (error) {
    console.error('Database connection error:', error);
    return false;
  }
}

export async function getUsers() {
  return apiCall('/users');
}

export async function getUserById(id) {
  return apiCall(`/users/${id}`);
}

export async function createUser(userData) {
  return apiCall('/users', {
    method: 'POST',
    body: JSON.stringify(userData)
  });
}

export async function updateUser(id, updates) {
  return apiCall(`/users/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates)
  });
}

export async function getProjects(vipLevel = 1) {
  return apiCall(`/projects?vipLevel=${vipLevel}`);
}

export async function getAllProjects() {
  return apiCall('/projects');
}

export async function createProject(projectData) {
  return apiCall('/projects', {
    method: 'POST',
    body: JSON.stringify(projectData)
  });
}

export async function updateProject(id, updates) {
  return apiCall(`/projects/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates)
  });
}

export async function deleteProject(id) {
  return apiCall(`/projects/${id}`, {
    method: 'DELETE'
  });
}

const DEFAULT_SETTINGS = {
  site_name: 'EarningsLLC',
  vip1_rate: '10',
  vip2_rate: '20',
  vip3_rate: '50',
  vip4_rate: '100',
  vip5_rate: '200',
  contact_email: 'admin@earningsllc.com',
  contact_phone: '+1 (555) 123-4567',
  contact_address: '123 Business Street\nNew York, NY 10001',
};

export async function getSettings() {
  try {
    const data = await apiCall('/settings');
    const settings = { ...DEFAULT_SETTINGS };
    if (Array.isArray(data)) {
      data.forEach(setting => {
        settings[setting.key] = setting.value;
      });
    }
    return settings;
  } catch (error) {
    return { ...DEFAULT_SETTINGS };
  }
}

export async function updateSetting(key, value) {
  return apiCall(`/settings/${key}`, {
    method: 'PUT',
    body: JSON.stringify({ value })
  });
}

export async function getUserProjects(userId) {
  return apiCall(`/users/${userId}/projects`);
}

export async function applyToProject(userId, projectId, hoursWorked = 0) {
  return apiCall('/user-projects', {
    method: 'POST',
    body: JSON.stringify({
      user_id: userId,
      project_id: projectId,
      hours_worked: hoursWorked
    })
  });
}

export async function getPayments(userId = null) {
  const endpoint = userId ? `/users/${userId}/payments` : '/payments';
  return apiCall(endpoint);
}

export async function createWithdrawal(userId, amount, method, accountDetails) {
  return apiCall('/withdrawals', {
    method: 'POST',
    body: JSON.stringify({
      user_id: userId,
      amount,
      method,
      account_details: accountDetails
    })
  });
}

export async function getWithdrawals(userId = null) {
  const endpoint = userId ? `/users/${userId}/withdrawals` : '/withdrawals';
  return apiCall(endpoint);
}

export async function updateWithdrawal(id, updates) {
  return apiCall(`/withdrawals/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates)
  });
}

export async function getAllUserProjects() {
  return apiCall('/user-projects');
}

export async function updateUserProject(id, updates) {
  return apiCall(`/user-projects/${id}`, {
    method: 'PUT',
    body: JSON.stringify(updates)
  });
}
