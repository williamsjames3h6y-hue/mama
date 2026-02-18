import { apiCall } from '../config/api.js';

export async function signUp(email, password, fullName) {
  const data = await apiCall('/auth/signup', {
    method: 'POST',
    body: JSON.stringify({ email, password, fullName }),
  });

  localStorage.setItem('currentUser', JSON.stringify(data.user));
  return data;
}

export async function signIn(email, password) {
  const data = await apiCall('/auth/signin', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  });

  localStorage.setItem('currentUser', JSON.stringify(data.user));
  return data;
}

export async function signOut() {
  localStorage.removeItem('currentUser');
}

export async function getCurrentUser() {
  const userStr = localStorage.getItem('currentUser');
  if (!userStr) return null;

  const user = JSON.parse(userStr);
  const freshUser = await apiCall(`/users/${user.id}`);

  if (freshUser) {
    localStorage.setItem('currentUser', JSON.stringify(freshUser));
  }

  return freshUser;
}

export function onAuthStateChange(callback) {
  const user = localStorage.getItem('currentUser');
  if (user) {
    callback('SIGNED_IN', JSON.parse(user));
  } else {
    callback('SIGNED_OUT', null);
  }

  return { data: { subscription: { unsubscribe: () => {} } } };
}
