<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { authState, clearAuth } from '@/authState'

const router = useRouter()

function handleLogout() {
  clearAuth()
  router.push({ name: 'home' }).catch(() => router.push('/'))
}

// computed-style check
const isAdmin = () =>
  authState.user?.roles?.includes('ROLE_ADMIN') ?? false
</script>

<template>
  <header class="header">
    <h1 class="title">
      <RouterLink to="/" class="logo-link">Paul's Cookbook</RouterLink>
    </h1>

    <div v-if="authState.user" class="userbar">
      <span class="greet">Hi, {{ authState.user.displayName }}</span>

      <!-- Show create recipe button only for admins -->
      <RouterLink
        v-if="isAdmin()"
        to="/recipe/new"
        class="logout-btn"
      >
        Create Recipe
      </RouterLink>

      <button class="logout-btn" @click="handleLogout">Log Out</button>
    </div>

    <RouterLink v-else to="/login" class="login-btn">Log In</RouterLink>
  </header>
</template>

<style scoped>
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background: #e6f0fa;
  border-bottom: 2px solid #d0e0f0
}

.title {
  margin: 0;
  color: #222;
  font-size: 1.5rem
}

.logo-link {
  color: inherit;
  text-decoration: none;
  cursor: pointer
}

.logo-link:hover {
  text-decoration: underline
}

.login-btn, .logout-btn {
  background: #007bff;
  color: #fff;
  border: none;
  padding: .5rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  transition: background .2s;
  text-decoration: none;
  display: inline-block;
  text-align: center
}

.login-btn:hover, .logout-btn:hover {
  background: #0056b3
}

.userbar {
  display: flex;
  align-items: center;
  gap: .75rem
}

.greet {
  color: #222;
  font-weight: 600
}
</style>
