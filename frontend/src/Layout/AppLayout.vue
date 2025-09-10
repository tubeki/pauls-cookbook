<script setup>
import SiteHeader from './SiteHeader.vue'

// Temporary hardcoded state
const user = null // or { name: 'Paul' }

function handleLogout() {
  console.log('logout click')
}
</script>

<template>
  <div class="app">
    <div class="container">
      <SiteHeader
        :user="user"
        @logout="handleLogout"
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
