import { signIn } from '../services/auth.js';
import { navigate } from '../main.js';

export function renderLoginPage(app) {
  app.innerHTML = `
    <div class="container" style="max-width: 500px; margin-top: 4rem;">
      <div class="card">
        <h1 style="margin-bottom: 1.5rem;">Login</h1>
        <div id="alert"></div>
        <form id="loginForm">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
          Don't have an account? <a href="#/register">Register here</a>
        </p>
      </div>
    </div>
  `;

  const form = document.getElementById('loginForm');
  form.addEventListener('submit', handleLogin);
}

async function handleLogin(e) {
  e.preventDefault();
  const alert = document.getElementById('alert');
  const form = e.target;
  const email = form.email.value;
  const password = form.password.value;

  try {
    await signIn(email, password);
    alert.innerHTML = '<div class="alert alert-success">Login successful!</div>';
    setTimeout(() => navigate('/'), 1000);
  } catch (error) {
    alert.innerHTML = `<div class="alert alert-error">${error.message}</div>`;
  }
}
