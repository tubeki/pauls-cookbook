<script setup>
import SiteHeader from './SiteHeader.vue'

// Temporary hardcoded state
const user = null // or { name: 'Paul' }

function handleLogin() {
  console.log('login click')
}

function handleLogout() {
  console.log('logout click')
}

function handleLogoClick() {
  console.log('logo click')
}
</script>

<template>
  <div class="app">
    <div class="container">
      <SiteHeader
        :user="user"
        @login="handleLogin"
        @logout="handleLogout"
        @logo-click="handleLogoClick"
      />
    </div>

    <main class="page container">
      <slot/>
    </main>
  </div>
</template>

<style scoped>
.app {
  min-height: 100vh;
  background: #f9f9f9;
  color: #333;
  font-family: Arial, Helvetica, sans-serif
}

.container {
  max-width: 960px;
  margin: 0 auto
}

.page {
  padding: 1rem 2rem
}
</style>
