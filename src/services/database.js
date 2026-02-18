import { supabase } from '../config/supabase.js';

export async function initializeDatabase() {
  try {
    const { data: tables } = await supabase.from('users').select('count', { count: 'exact', head: true });
    console.log('Database connection verified');
    return true;
  } catch (error) {
    console.log('Database needs initialization');
    return false;
  }
}

export async function getUsers() {
  const { data, error } = await supabase
    .from('users')
    .select('*')
    .order('created_at', { ascending: false });

  if (error) throw error;
  return data || [];
}

export async function getUserById(id) {
  const { data, error } = await supabase
    .from('users')
    .select('*')
    .eq('id', id)
    .maybeSingle();

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
    .lte('vip_level_required', vipLevel)
    .eq('status', 'open')
    .order('created_at', { ascending: false });

  if (error) throw error;
  return data || [];
}

export async function getAllProjects() {
  const { data, error } = await supabase
    .from('projects')
    .select('*')
    .order('created_at', { ascending: false });

  if (error) throw error;
  return data || [];
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
}

export async function getSettings() {
  const { data, error } = await supabase
    .from('site_settings')
    .select('*');

  if (error) throw error;

  const settings = {};
  if (data) {
    data.forEach(item => {
      settings[item.key] = item.value;
    });
  }
  return settings;
}

export async function updateSetting(key, value) {
  const { data, error } = await supabase
    .from('site_settings')
    .upsert({ key, value }, { onConflict: 'key' })
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
  return data || [];
}

export async function applyToProject(userId, projectId) {
  const { data, error } = await supabase
    .from('user_projects')
    .insert([{
      user_id: userId,
      project_id: projectId,
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
  return data || [];
}
