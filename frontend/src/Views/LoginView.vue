<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { authState, setToken } from '@/authState';

const router = useRouter();
const email = ref('');
const password = ref('');
const loading = ref(false);

async function onSubmit() {
  loading.value = true;
  try {
    // 1) Login
    const res = await fetch(`${import.meta.env.VITE_API_BASE_URL || ''}/login_check`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });

    if (!res.ok) {
      console.error('Login failed', res.status, await safeJson(res));
      loading.value = false;
      return;
    }

    const data = await res.json();
    const token = data.token || data.access_token; // support both shapes
    if (!token) {
      console.error('No token in login response', data);
      loading.value = false;
      return;
    }

    // 2) Save token
    setToken(token);

    console.log(authState.token);

    // 3) Fetch current user
    const meRes = await fetch(`${import.meta.env.VITE_API_BASE_URL || ''}/me`, {
      headers: { Authorization: `Bearer ${token}` },
    });

    if (!meRes.ok) {
      console.error('Fetching /api/me failed', meRes.status, await safeJson(meRes));
      loading.value = false;
      return;
    }

    authState.user = await meRes.json();

    console.log(authState.user);

    // 4) Redirect to Home
    try {
      await router.push({ name: 'home' });
    } catch {
      await router.push('/');
    }
  } catch (e) {
    console.error('Unexpected error during login', e);
  } finally {
    loading.value = false;
  }
}

async function safeJson(res) {
  try { return await res.json(); } catch { return {}; }
}
</script>

<template>
  <div class="container">
    <main>
      <form class="login-form" @submit.prevent="onSubmit">
        <h2>Login</h2>
        <div>
          <label for="email">Email</label>
          <input v-model="email" type="email" id="email" placeholder="Enter your email" required />
        </div>
        <div>
          <label for="password">Password</label>
          <input v-model="password" type="password" id="password" placeholder="Enter your password" required />
        </div>
        <button :disabled="loading" type="submit">
          {{ loading ? 'Logging in…' : 'Log In' }}
        </button>
        <p class="footer-text">
          Don’t have an account? <a href="#">Sign up</a>
        </p>
      </form>
    </main>
  </div>
</template>

<style scoped>
.container{max-width:960px;margin:0 auto}
main{padding:2rem;display:flex;justify-content:center}
.login-form{background:#fff;padding:2rem;border:1px solid #ddd;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,.05);width:100%;max-width:400px}
.login-form h2{margin-top:0;color:#222}
label{display:block;margin-bottom:.5rem;font-weight:bold}
input{width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;margin-bottom:1rem;font-size:1rem}
button{background:#007bff;color:#fff;border:none;padding:.7rem 1.2rem;border-radius:10px;cursor:pointer;font-size:1rem;width:100%}
button:hover{background:#0056b3}
.footer-text{text-align:center;margin-top:1rem;font-size:.9rem;color:#555}
.footer-text a{color:#007bff;text-decoration:none}
.footer-text a:hover{text-decoration:underline}
</style>
