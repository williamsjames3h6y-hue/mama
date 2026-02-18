import { navigate } from '../main.js';

export function renderLandingPage(app, settings) {
  app.innerHTML = `
    <div class="landing-page">
      <header class="landing-header">
        <div class="navbar-brand">EarningsLLC</div>
        <div style="display: flex; gap: 1rem;">
          <button class="btn btn-outline" onclick="window.location.hash='/login'">Login</button>
          <button class="btn btn-primary" onclick="window.location.hash='/register'">Sign Up</button>
        </div>
      </header>

      <section class="landing-hero">
        <h1>Earn Money Online with Real Projects</h1>
        <p>Join thousands of professionals earning money by completing high-quality projects</p>
        <button class="btn btn-primary btn-lg" onclick="window.location.hash='/register'" style="padding: 1rem 2.5rem; font-size: 1.125rem;">Get Started Now</button>
      </section>

      <section class="features-grid">
        <div class="feature-card">
          <h3>💰 High Pay Rates</h3>
          <p>Earn from $10 to $200 per hour based on your skill level</p>
        </div>
        <div class="feature-card">
          <h3>🚀 Flexible Schedule</h3>
          <p>Work on your own time, from anywhere in the world</p>
        </div>
        <div class="feature-card">
          <h3>⭐ VIP Levels</h3>
          <p>Progress through 5 VIP levels and unlock higher-paying projects</p>
        </div>
      </section>

      <section class="vip-levels">
        <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 1rem;">VIP Level System</h2>
        <p style="text-align: center; font-size: 1.125rem; opacity: 0.9; margin-bottom: 2rem;">Start at Level 1 and work your way up to unlock premium projects</p>

        <div class="vip-grid">
          <div class="vip-card">
            <div class="badge badge-vip" style="font-size: 1.125rem; padding: 0.5rem 1rem;">VIP 1</div>
            <h3 style="margin: 1rem 0;">Beginner</h3>
            <p style="font-size: 1.5rem; font-weight: 700;">$${settings.vip1_rate || '10'}/hr</p>
            <p style="opacity: 0.9; font-size: 0.875rem;">Entry-level tasks</p>
          </div>
          <div class="vip-card">
            <div class="badge badge-vip" style="font-size: 1.125rem; padding: 0.5rem 1rem;">VIP 2</div>
            <h3 style="margin: 1rem 0;">Intermediate</h3>
            <p style="font-size: 1.5rem; font-weight: 700;">$${settings.vip2_rate || '20'}/hr</p>
            <p style="opacity: 0.9; font-size: 0.875rem;">Moderate complexity</p>
          </div>
          <div class="vip-card">
            <div class="badge badge-vip" style="font-size: 1.125rem; padding: 0.5rem 1rem;">VIP 3</div>
            <h3 style="margin: 1rem 0;">Advanced</h3>
            <p style="font-size: 1.5rem; font-weight: 700;">$${settings.vip3_rate || '50'}/hr</p>
            <p style="opacity: 0.9; font-size: 0.875rem;">Professional projects</p>
          </div>
          <div class="vip-card">
            <div class="badge badge-vip" style="font-size: 1.125rem; padding: 0.5rem 1rem;">VIP 4</div>
            <h3 style="margin: 1rem 0;">Expert</h3>
            <p style="font-size: 1.5rem; font-weight: 700;">$${settings.vip4_rate || '100'}/hr</p>
            <p style="opacity: 0.9; font-size: 0.875rem;">High-value work</p>
          </div>
          <div class="vip-card">
            <div class="badge badge-vip" style="font-size: 1.125rem; padding: 0.5rem 1rem;">VIP 5</div>
            <h3 style="margin: 1rem 0;">Master</h3>
            <p style="font-size: 1.5rem; font-weight: 700;">$${settings.vip5_rate || '200'}/hr</p>
            <p style="opacity: 0.9; font-size: 0.875rem;">Elite opportunities</p>
          </div>
        </div>
      </section>

      <footer class="footer">
        <h3 style="margin-bottom: 1.5rem;">Contact Us</h3>
        <p>Email: ${settings.contact_email || 'admin@earningsllc.com'}</p>
        <p>Phone: ${settings.contact_phone || '+1 (555) 123-4567'}</p>
        <p style="margin-top: 1rem;">
          ${settings.contact_address ? settings.contact_address.replace(/\n/g, '<br>') : '123 Business Street<br>New York, NY 10001'}
        </p>
        <p style="margin-top: 2rem; opacity: 0.7;">© 2024 EarningsLLC. All rights reserved.</p>
      </footer>
    </div>
  `;
}
