// Tiny reactive auth state for the whole app
import { reactive } from 'vue';

export const authState = reactive({
  token: localStorage.getItem('access_token') || null,
  user: null,
});

export function setToken(token) {
  authState.token = token;
  localStorage.setItem('access_token', token);
}

export function clearAuth() {
  authState.token = null;
  authState.user = null;
  localStorage.removeItem('access_token');
}
