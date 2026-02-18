import express from 'express';
import cors from 'cors';
import mysql from 'mysql2/promise';
import bcrypt from 'bcryptjs';

const app = express();
app.use(cors());
app.use(express.json());

const pool = mysql.createPool({
  host: process.env.MYSQL_HOST || 'localhost',
  port: process.env.MYSQL_PORT || 3306,
  user: process.env.MYSQL_USER || 'root',
  password: process.env.MYSQL_PASSWORD || '',
  database: process.env.MYSQL_DATABASE || 'earningsllc',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

app.post('/api/auth/signup', async (req, res) => {
  try {
    const { email, password, fullName } = req.body;
    const hashedPassword = await bcrypt.hash(password, 10);
    const referralCode = 'REF' + Math.random().toString(36).substring(2, 10).toUpperCase();

    const [result] = await pool.execute(
      `INSERT INTO users (email, password, full_name, role, vip_level, balance, total_earned, referral_code, created_at)
       VALUES (?, ?, ?, 'user', 1, 0, 0, ?, NOW())`,
      [email, hashedPassword, fullName, referralCode]
    );

    const [users] = await pool.execute('SELECT id, email, full_name, role, vip_level, balance, total_earned, referral_code, created_at FROM users WHERE id = ?', [result.insertId]);
    res.json({ user: users[0] });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.post('/api/auth/signin', async (req, res) => {
  try {
    const { email, password } = req.body;
    const [users] = await pool.execute('SELECT * FROM users WHERE email = ?', [email]);

    if (users.length === 0) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    const user = users[0];
    const isValid = await bcrypt.compare(password, user.password);

    if (!isValid) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    delete user.password;
    res.json({ user });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/users/:id', async (req, res) => {
  try {
    const [users] = await pool.execute('SELECT id, email, full_name, role, vip_level, balance, total_earned, referral_code, created_at FROM users WHERE id = ?', [req.params.id]);
    res.json(users[0] || null);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/users', async (req, res) => {
  try {
    const [rows] = await pool.execute('SELECT id, email, full_name, role, vip_level, balance, total_earned, referral_code, created_at FROM users ORDER BY created_at DESC');
    res.json(rows);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.put('/api/users/:id', async (req, res) => {
  try {
    const fields = Object.keys(req.body).map(key => `${key} = ?`).join(', ');
    const values = [...Object.values(req.body), req.params.id];

    await pool.execute(`UPDATE users SET ${fields} WHERE id = ?`, values);
    const [users] = await pool.execute('SELECT id, email, full_name, role, vip_level, balance, total_earned, referral_code, created_at FROM users WHERE id = ?', [req.params.id]);
    res.json(users[0]);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/projects', async (req, res) => {
  try {
    const vipLevel = req.query.vipLevel || 1;
    const [rows] = await pool.execute(
      'SELECT * FROM projects WHERE vip_level_required <= ? AND status = "open" ORDER BY created_at DESC',
      [vipLevel]
    );
    res.json(rows);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/projects/all', async (req, res) => {
  try {
    const [rows] = await pool.execute('SELECT * FROM projects ORDER BY created_at DESC');
    res.json(rows);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.post('/api/projects', async (req, res) => {
  try {
    const { title, description, project_type, rate_min, rate_max, vip_level_required, status } = req.body;
    const [result] = await pool.execute(
      `INSERT INTO projects (title, description, project_type, rate_min, rate_max, vip_level_required, status, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())`,
      [title, description, project_type, rate_min, rate_max, vip_level_required, status || 'open']
    );

    const [rows] = await pool.execute('SELECT * FROM projects WHERE id = ?', [result.insertId]);
    res.json(rows[0]);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.put('/api/projects/:id', async (req, res) => {
  try {
    const fields = Object.keys(req.body).map(key => `${key} = ?`).join(', ');
    const values = [...Object.values(req.body), req.params.id];

    await pool.execute(`UPDATE projects SET ${fields} WHERE id = ?`, values);
    const [rows] = await pool.execute('SELECT * FROM projects WHERE id = ?', [req.params.id]);
    res.json(rows[0]);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.delete('/api/projects/:id', async (req, res) => {
  try {
    await pool.execute('DELETE FROM projects WHERE id = ?', [req.params.id]);
    res.json({ success: true });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/user-projects/:userId', async (req, res) => {
  try {
    const [rows] = await pool.execute(
      `SELECT up.*, p.title, p.description, p.project_type, p.rate_min, p.rate_max, p.vip_level_required, p.status as project_status
       FROM user_projects up
       JOIN projects p ON up.project_id = p.id
       WHERE up.user_id = ?
       ORDER BY up.submitted_at DESC`,
      [req.params.userId]
    );

    const formatted = rows.map(row => ({
      id: row.id,
      user_id: row.user_id,
      project_id: row.project_id,
      status: row.status,
      submitted_at: row.submitted_at,
      projects: {
        id: row.project_id,
        title: row.title,
        description: row.description,
        project_type: row.project_type,
        rate_min: row.rate_min,
        rate_max: row.rate_max,
        vip_level_required: row.vip_level_required,
        status: row.project_status
      }
    }));

    res.json(formatted);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.post('/api/user-projects', async (req, res) => {
  try {
    const { userId, projectId } = req.body;
    const [result] = await pool.execute(
      `INSERT INTO user_projects (user_id, project_id, status, submitted_at)
       VALUES (?, ?, 'submitted', NOW())`,
      [userId, projectId]
    );

    const [rows] = await pool.execute('SELECT * FROM user_projects WHERE id = ?', [result.insertId]);
    res.json(rows[0]);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/payments', async (req, res) => {
  try {
    let query = 'SELECT * FROM payments';
    let params = [];

    if (req.query.userId) {
      query += ' WHERE user_id = ?';
      params.push(req.query.userId);
    }

    query += ' ORDER BY created_at DESC';

    const [rows] = await pool.execute(query, params);
    res.json(rows);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.get('/api/settings', async (req, res) => {
  try {
    const [rows] = await pool.execute('SELECT * FROM site_settings');
    const settings = {};
    rows.forEach(item => {
      settings[item.key] = item.value;
    });
    res.json(settings);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

app.put('/api/settings/:key', async (req, res) => {
  try {
    const { value } = req.body;
    await pool.execute(
      `INSERT INTO site_settings (\`key\`, value) VALUES (?, ?)
       ON DUPLICATE KEY UPDATE value = ?`,
      [req.params.key, value, value]
    );

    const [rows] = await pool.execute('SELECT * FROM site_settings WHERE `key` = ?', [req.params.key]);
    res.json(rows[0]);
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
});

const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
