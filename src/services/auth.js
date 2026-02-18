import { apiCall } from '../config/api.js';

let currentUser = null;
let authChangeCallbacks = [];

export async function signUp(email, password, fullName) {
  const data = await apiCall('/auth/signup', {
    method: 'POST',
    body: JSON.stringify({ email, password, full_name: fullName })
  });

  if (data.user) {
    currentUser = data.user;
    notifyAuthChange('SIGNED_UP', data.user);
  }

  return data;
}

export async function signIn(email, password) {
  const data = await apiCall('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password })
  });

  if (data.user) {
    currentUser = data.user;
    localStorage.setItem('user', JSON.stringify(data.user));
    localStorage.setItem('token', data.token);
    notifyAuthChange('SIGNED_IN', data.user);
  }

  return data;
}

export async function signOut() {
  await apiCall('/auth/logout', { method: 'POST' });
  currentUser = null;
  localStorage.removeItem('user');
  localStorage.removeItem('token');
  notifyAuthChange('SIGNED_OUT', null);
}

export async function getCurrentUser() {
  if (currentUser) {
    return currentUser;
  }

  const storedUser = localStorage.getItem('user');
  const token = localStorage.getItem('token');

  if (!storedUser || !token) {
    return null;
  }

  try {
    const user = JSON.parse(storedUser);
    const userData = await apiCall(`/users/${user.id}`);
    currentUser = userData;
    return userData;
  } catch (error) {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    return null;
  }
}

export function onAuthStateChange(callback) {
  authChangeCallbacks.push(callback);

  return {
    unsubscribe: () => {
      authChangeCallbacks = authChangeCallbacks.filter(cb => cb !== callback);
    }
  };
}

function notifyAuthChange(event, user) {
  authChangeCallbacks.forEach(callback => {
    callback(event, user);
  });
}

export async function updateProfile(userId, updates) {
  return apiCall(`/users/${userId}`, {
    method: 'PUT',
    body: JSON.stringify(updates)
  });
}

export async function resetPassword(email) {
  return apiCall('/auth/reset-password', {
    method: 'POST',
    body: JSON.stringify({ email })
  });
}
