import { signUp } from '../services/auth.js';
import { navigate } from '../main.js';

export function renderRegisterPage(app) {
  app.innerHTML = `
    <div class="container" style="max-width: 500px; margin-top: 4rem;">
      <div class="card">
        <h1 style="margin-bottom: 1.5rem;">Create Account</h1>
        <div id="alert"></div>
        <form id="registerForm">
          <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
          Already have an account? <a href="#/login">Login here</a>
        </p>
      </div>
    </div>
  `;

  const form = document.getElementById('registerForm');
  form.addEventListener('submit', handleRegister);
}

async function handleRegister(e) {
  e.preventDefault();
  const alert = document.getElementById('alert');
  const form = e.target;
  const fullName = form.fullName.value;
  const email = form.email.value;
  const password = form.password.value;

  try {
    await signUp(email, password, fullName);
    alert.innerHTML = '<div class="alert alert-success">Registration successful! Logging you in...</div>';
    setTimeout(() => navigate('/'), 1500);
  } catch (error) {
    alert.innerHTML = `<div class="alert alert-error">${error.message}</div>`;
  }
}
