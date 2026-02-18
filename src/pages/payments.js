import { getPayments } from '../services/database.js';
import { signOut } from '../services/auth.js';
import { navigate } from '../main.js';

export async function renderPaymentsPage(app, user) {
  const payments = await getPayments(user.id);

  app.innerHTML = `
    ${renderNavbar(user)}
    <div class="container">
      <h1 style="margin-bottom: 2rem;">Payment History</h1>

      <div class="card">
        ${payments.length === 0 ? '<p>No payment history yet.</p>' : `
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              ${payments.map(payment => `
                <tr>
                  <td>${new Date(payment.created_at).toLocaleDateString()}</td>
                  <td><span class="badge">${payment.type}</span></td>
                  <td style="font-weight: 700; color: #3498db;">$${payment.amount.toFixed(2)}</td>
                  <td><span class="badge badge-${payment.status === 'completed' ? 'open' : 'closed'}">${payment.status}</span></td>
                  <td>${payment.description || '-'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `}
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

window.handleLogout = async function() {
  await signOut();
  navigate('/');
};
