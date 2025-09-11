<template>
  <main class="page">
    <nav class="breadcrumb">
      <RouterLink class="link" to="/">Home</RouterLink> /
      <span class="muted">Recipes</span> /
      <span>Edit</span>
    </nav>

    <article class="recipe-card">
      <h1 class="recipe-title">Edit Recipe</h1>

      <div v-if="!isLoggedIn" class="notice">
        You must be signed in to edit a recipe.
        <RouterLink to="/login" class="link">Go to login</RouterLink>
      </div>

      <div v-else-if="!isAdmin" class="notice error">
        You need administrator privileges (ROLE_ADMIN) to edit recipes.
      </div>

      <p v-else-if="error" class="muted">Failed to load recipe: {{ error }}</p>

      <div v-else-if="!recipe" class="notice">Loading…</div>

      <div v-else class="form-wrap">
        <RecipeForm :initialData="recipe" @submit="onSubmit" />
      </div>
    </article>
  </main>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { authState } from '@/authState'
import RecipeForm from '@/components/RecipeForm.vue'

const route = useRoute()
const router = useRouter()
const apiBase = import.meta.env.VITE_API_BASE_URL

const isLoggedIn = computed(() => !!authState.token && !!authState.user)
const isAdmin = computed(() => authState.user?.roles?.includes('ROLE_ADMIN') ?? false)

const recipe = ref(null)
const error = ref('')
const posting = ref(false)

async function loadRecipe() {
  try {
    const res = await fetch(`${apiBase}/recipes/${route.params.id}`)
    if (!res.ok) throw new Error(`${res.status} ${res.statusText}`)
    recipe.value = await res.json()
  } catch (e) {
    error.value = e?.message || 'Unknown error'
  }
}

onMounted(loadRecipe)

async function onSubmit(payload) {
  if (!apiBase) { error.value = 'API base URL is not configured.'; return }
  if (!authState.token) { error.value = 'Missing auth token.'; return }

  posting.value = true
  error.value = ''

  try {
    const res = await fetch(`${apiBase}/recipes/${route.params.id}`, {
      method: 'PUT', // or PATCH if your API prefers
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${authState.token}`,
      },
      body: JSON.stringify(payload),
    })

    if (!res.ok) {
      const text = await res.text().catch(() => '')
      throw new Error(text || `Update failed (${res.status})`)
    }

    const updated = await res.json()
    try {
      await router.push({ name: 'RecipeView', params: { id: updated.id } })
    } catch {
      await router.push(`/recipe/${updated.id}`)
    }
  } catch (e) {
    error.value = e?.message || 'Failed to update recipe.'
    console.error(e)
  } finally {
    posting.value = false
  }
}
</script>

<style scoped>
.page{padding:1rem 2rem}

.breadcrumb{
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

.btn{
  background:#007bff;
  color:#fff;
  border:none;
  padding:.55rem 1rem;
  border-radius:10px;
  cursor:pointer;
  font:inherit
}
.btn:hover{background:#0056b3}
</style>
