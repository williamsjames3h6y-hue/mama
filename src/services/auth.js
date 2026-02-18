import { supabase } from '../config/supabase.js';
import { getUserById, createUser } from './database.js';

export async function signUp(email, password, fullName) {
  const { data: authData, error: authError } = await supabase.auth.signUp({
    email,
    password,
  });

  if (authError) throw authError;

  if (authData.user) {
    const referralCode = generateReferralCode();

    await createUser({
      id: authData.user.id,
      email,
      full_name: fullName,
      role: 'user',
      vip_level: 1,
      balance: 0,
      total_earned: 0,
      referral_code: referralCode
    });
  }

  return authData;
}

export async function signIn(email, password) {
  const { data, error } = await supabase.auth.signInWithPassword({
    email,
    password,
  });

  if (error) throw error;
  return data;
}

export async function signOut() {
  const { error } = await supabase.auth.signOut();
  if (error) throw error;
}

export async function getCurrentUser() {
  const { data: { session } } = await supabase.auth.getSession();

  if (!session) return null;

  const user = await getUserById(session.user.id);
  return user;
}

export function onAuthStateChange(callback) {
  return supabase.auth.onAuthStateChange((event, session) => {
    (async () => {
      if (session) {
        const user = await getUserById(session.user.id);
        callback(event, user);
      } else {
        callback(event, null);
      }
    })();
  });
}

function generateReferralCode() {
  return 'REF' + Math.random().toString(36).substring(2, 10).toUpperCase();
}
