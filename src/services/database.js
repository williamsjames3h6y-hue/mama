import { supabase } from '../config/supabase.js';

export async function initializeDatabase() {
  try {
    const { error } = await supabase.from('users').select('id').limit(1);
    if (error) throw error;
    console.log('Database connection verified');
    return true;
  } catch (error) {
    console.error('Database connection error:', error);
    return false;
  }
}

export async function getUsers() {
  const { data, error } = await supabase
    .from('users')
    .select('*')
    .order('created_at', { ascending: false });

  if (error) throw error;
  return data;
}

export async function getUserById(id) {
  const { data, error } = await supabase
    .from('users')
    .select('*')
    .eq('id', id)
    .single();

  if (error) throw error;
  return data;
}

export async function createUser(userData) {
  const { data, error } = await supabase
    .from('users')
    .insert([userData])
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function updateUser(id, updates) {
  const { data, error } = await supabase
    .from('users')
    .update(updates)
    .eq('id', id)
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function getProjects(vipLevel = 1) {
  const { data, error } = await supabase
    .from('projects')
    .select('*')
    .eq('status', 'open')
    .lte('vip_level_required', vipLevel)
    .order('created_at', { ascending: false });

  if (error) throw error;
  return data;
}

export async function getAllProjects() {
  const { data, error } = await supabase
    .from('projects')
    .select('*')
    .order('created_at', { ascending: false });

  if (error) throw error;
  return data;
}

export async function createProject(projectData) {
  const { data, error } = await supabase
    .from('projects')
    .insert([projectData])
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function updateProject(id, updates) {
  const { data, error } = await supabase
    .from('projects')
    .update(updates)
    .eq('id', id)
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function deleteProject(id) {
  const { error } = await supabase
    .from('projects')
    .delete()
    .eq('id', id);

  if (error) throw error;
  return { success: true };
}

export async function getSettings() {
  const { data, error } = await supabase
    .from('site_settings')
    .select('*');

  if (error) throw error;

  const settings = {};
  data.forEach(setting => {
    settings[setting.key] = setting.value;
  });

  return settings;
}

export async function updateSetting(key, value) {
  const { data, error } = await supabase
    .from('site_settings')
    .update({ value })
    .eq('key', key)
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function getUserProjects(userId) {
  const { data, error } = await supabase
    .from('user_projects')
    .select(`
      *,
      projects (*)
    `)
    .eq('user_id', userId)
    .order('submitted_at', { ascending: false });

  if (error) throw error;
  return data;
}

export async function applyToProject(userId, projectId, hoursWorked = 0) {
  const { data, error } = await supabase
    .from('user_projects')
    .insert([{
      user_id: userId,
      project_id: projectId,
      hours_worked: hoursWorked,
      status: 'submitted'
    }])
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function getPayments(userId = null) {
  let query = supabase
    .from('payments')
    .select('*')
    .order('created_at', { ascending: false });

  if (userId) {
    query = query.eq('user_id', userId);
  }

  const { data, error } = await query;

  if (error) throw error;
  return data;
}

export async function createWithdrawal(userId, amount, method, accountDetails) {
  const { data, error } = await supabase
    .from('withdrawals')
    .insert([{
      user_id: userId,
      amount,
      method,
      account_details: accountDetails,
      status: 'pending'
    }])
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function getWithdrawals(userId = null) {
  let query = supabase
    .from('withdrawals')
    .select('*')
    .order('created_at', { ascending: false });

  if (userId) {
    query = query.eq('user_id', userId);
  }

  const { data, error } = await query;

  if (error) throw error;
  return data;
}

export async function updateWithdrawal(id, updates) {
  const { data, error } = await supabase
    .from('withdrawals')
    .update(updates)
    .eq('id', id)
    .select()
    .single();

  if (error) throw error;
  return data;
}

export async function getAllUserProjects() {
  const { data, error } = await supabase
    .from('user_projects')
    .select(`
      *,
      users!user_projects_user_id_fkey (
        id,
        email,
        full_name
      ),
      projects (
        id,
        title,
        description
      )
    `)
    .order('submitted_at', { ascending: false });

  if (error) throw error;
  return data;
}

export async function updateUserProject(id, updates) {
  const { data, error } = await supabase
    .from('user_projects')
    .update(updates)
    .eq('id', id)
    .select()
    .single();

  if (error) throw error;
  return data;
}
