<script setup>
import { computed, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { authState } from '@/authState'
import RecipeForm from '@/components/RecipeForm.vue'

const router = useRouter()

const isLoggedIn = computed(() => !!authState.token && !!authState.user)
const isAdmin = computed(() => authState.user?.roles?.includes('ROLE_ADMIN') ?? false)

const apiBase = import.meta.env.VITE_API_BASE_URL
const posting = ref(false)
const error = ref(null)

async function onSubmit(payload) {
  // You’ll see the exact payload we send
  console.log('Create payload:', payload)

  if (!apiBase) {
    error.value = 'API base URL is not configured.'
    return
  }
  if (!authState.token) {
    error.value = 'Missing auth token.'
    return
  }

  posting.value = true
  error.value = null

  try {
    const res = await fetch(`${apiBase}/recipes`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${authState.token}`,
      },
      body: JSON.stringify(payload),
    })

    if (res.status !== 201) {
      const text = await res.text().catch(() => '')
      throw new Error(text || `Request failed with ${res.status}`)
    }

    const created = await res.json()

    // Redirect to the new recipe page.
    // Prefer a named route if you have one, else fall back to path.
    try {
      await router.push({ name: 'RecipeView', params: { id: created.id } })
    } catch {
      await router.push(`/recipe/${created.id}`)
    }
  } catch (e) {
    error.value = e?.message || 'Failed to create recipe.'
    console.error(e)
  } finally {
    posting.value = false
  }
}
</script>

<template>
  <main class="page">
    <nav class="breadcrumb">
      <RouterLink class="link" to="/">Home</RouterLink> /
      <span class="muted">Recipes</span> /
      <span>New</span>
    </nav>

    <article class="recipe-card">
      <h1 class="recipe-title">Create Recipe</h1>

      <div v-if="!isLoggedIn" class="notice">
        You must be signed in to create a recipe.
        <RouterLink to="/login" class="link">Go to login</RouterLink>
      </div>

      <div v-else-if="!isAdmin" class="notice error">
        You need administrator privileges (ROLE_ADMIN) to create recipes.
      </div>

      <div v-else class="form-wrap">
        <RecipeForm @submit="onSubmit" />
      </div>
    </article>
  </main>
</template>

<style scoped>
.page{padding:1rem 2rem}

.breadcrumb {
  font-size:.9rem;
  color:#666;
  margin-bottom:.75rem
}

.recipe-card{
  background:#fff;
  border:1px solid #ddd;
  border-radius:10px;
  box-shadow:0 1px 2px rgba(0,0,0,.05);
  padding:1.25rem
}

.recipe-title{
  margin:.25rem 0 1rem 0;
  color:#222;
  font-size:1.6rem
}

.notice{
  background:#f8fbff;
  border:1px solid #dfeafd;
  color:#224;
  border-radius:10px;
  padding:1rem;
  margin:.75rem 0
}
.notice.error{
  background:#fff7f7;
  border-color:#ffd7d7;
  color:#821
}

.muted{color:#888}

a.link{color:#0056b3;text-decoration:none}
a.link:hover{text-decoration:underline}
</style>

