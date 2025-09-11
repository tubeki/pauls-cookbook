<template>
  <main class="page">
    <nav class="breadcrumb">
      <RouterLink class="link" to="/">Home</RouterLink> /
      <span class="muted">Recipes</span> /
      <span v-if="recipe">{{ recipe.title }}</span>
      <span v-else>Loading…</span>
    </nav>

    <p v-if="error" class="muted">Failed to load recipe: {{ error }}</p>

    <article v-else-if="recipe" class="recipe-card">
      <h1 class="recipe-title">{{ recipe.title }}</h1>
      <div class="badge" v-if="recipe.averageRating">
        ⭐ {{ Number(recipe.averageRating).toFixed(1) }}
      </div>

      <div v-if="canEdit" style="margin-top:.75rem">
        <RouterLink
          class="btn"
          :to="`/recipe/${recipe.id}/edit`"
        >
          Edit Recipe
        </RouterLink>
      </div>

      <div class="meta">
        <span>By {{ recipe.author.displayName }}</span>
        <span>Created {{ formatDate(recipe.createdAt) }}</span>
        <span v-if="recipe.updatedAt">Updated {{ formatDate(recipe.updatedAt) }}</span>
      </div>

      <section class="section">
        <h3>Description</h3>
        <p class="desc">{{ recipe.description }}</p>
      </section>

      <section class="section" v-if="canRate && recipe">
        <h3>Your rating</h3>

        <form class="rate" @submit.prevent="submitRating">
          <fieldset role="radiogroup" aria-label="Rate this recipe" class="stars">
            <label
              v-for="n in 5"
              :key="n"
              :for="`rate-${n}`"
              :class="{ 'is-selected': selectedRating === String(n) }"
              style="margin-right:.5rem; cursor:pointer"
            >
              <input
                type="radio"
                name="value"
                :id="`rate-${n}`"
                :value="String(n)"
                v-model="selectedRating"
                :disabled="ratingPosting"
                style="margin-right:.25rem"
              />
              {{ n }}
            </label>
          </fieldset>

          <button type="submit" class="btn" :disabled="ratingPosting || !selectedRating">
            {{ ratingPosting ? 'Submitting…' : 'Submit rating' }}
          </button>

          <p v-if="ratingError" class="muted" style="margin-top:.5rem">Error: {{ ratingError }}</p>
        </form>

        <p class="muted" style="margin-top:.5rem">
          Average rating: {{ recipe.averageRating ? Number(recipe.averageRating).toFixed(1) : '—' }}
        </p>
      </section>

      <section class="section">
        <h3>Ingredients</h3>
        <div class="list">
          <ul class="ingredients">
            <li v-for="ing in recipe.ingredients" :key="ing.id">
              {{ ing.name }}
            </li>
          </ul>
        </div>
      </section>

      <section class="section">
        <h3>Steps</h3>
        <div class="list">
          <ol class="steps">
            <li v-for="step in recipe.steps" :key="step.id">{{ step.instruction }}</li>
          </ol>
        </div>
      </section>

      <!-- Add a comment (ROLE_USER only) -->
      <section class="section" v-if="canComment">
        <h3>Add a comment</h3>
        <form class="comment-form" @submit.prevent="submitComment">
          <label for="comment" class="muted">Share your thoughts</label>
          <textarea
            id="comment"
            name="body"
            placeholder="Write a comment..."
            v-model="newComment"
            :disabled="posting"
            required
          ></textarea>
          <div class="comment-actions">
            <span class="muted">
              Posting as <strong>{{ userDisplayName }}</strong>
            </span>
            <button type="submit" class="btn" :disabled="posting || !newComment.trim()">
              {{ posting ? 'Posting…' : 'Post Comment' }}
            </button>
          </div>
          <p v-if="postError" class="muted">Error: {{ postError }}</p>
        </form>
      </section>

      <section class="section" v-else>
        <p class="muted" style="margin-top:.5rem">
          Want to leave a comment?
          <RouterLink class="link" to="/login">Sign in</RouterLink>.
        </p>
      </section>

      <section class="section">
        <h3>Comments</h3>
        <div class="comments" v-if="recipe.comments && recipe.comments.length">
          <div class="comment" v-for="c in recipe.comments" :key="c.id">
            <div class="head">
              <div>{{ c.author.displayName }}</div>
              <div class="muted">{{ formatDate(c.createdAt) }}</div>
            </div>
            <div class="body">{{ c.body }}</div>
          </div>
        </div>
        <p class="muted" v-else>No comments yet.</p>
      </section>
    </article>
  </main>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import { useRoute } from "vue-router";
import {authState} from "@/authState.js";

const route = useRoute();
const recipe = ref(null);
const error = ref("");

const token = authState.token;
const storedUser = authState.user;
const roles = storedUser?.roles ?? [];

const canComment = computed(() => Boolean(token) && roles.includes("ROLE_USER"));
const canEdit = computed(() => Boolean(token) && roles.includes('ROLE_ADMIN'))

const userDisplayName = computed(() => storedUser?.displayName || "User");

// NEW state
const selectedRating = ref(null)
const ratingPosting = ref(false)
const ratingError = ref("")

// Show only for logged-in users (same rule as comments)
const canRate = computed(() => Boolean(token) && roles.includes("ROLE_USER"))

// --- comment form state ---
const newComment = ref("");
const posting = ref(false);
const postError = ref("");

// --- utils ---
function formatDate(iso) {
  if (!iso) return "";
  return new Intl.DateTimeFormat(undefined, {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    hour12: false
  }).format(new Date(iso));
}

// Helper: find current user's rating on the loaded recipe and prefill
function preloadUserRating() {
  if (!recipe.value || !storedUser?.id) return;
  const mine = (recipe.value.ratings || []).find(r =>
    r.user && r.user.id === storedUser.id
  );
  selectedRating.value = mine ? String(mine.score) : null;
}

// --- load recipe ---
// Call after loading the recipe
async function loadRecipe() {
  const res = await fetch(`${import.meta.env.VITE_API_BASE_URL}/recipes/${route.params.id}`)
  if (!res.ok) throw new Error(`${res.status} ${res.statusText}`)
  recipe.value = await res.json()
  preloadUserRating()
}


onMounted(async () => {
  try {
    await loadRecipe();
  } catch (e) {
    error.value = e.message || "Unknown error";
  }
});

// --- submit comment ---
async function submitComment() {
  postError.value = "";
  posting.value = true;
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/recipes/${route.params.id}/comments`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          ...(token ? { Authorization: `Bearer ${token}` } : {})
        },
        body: JSON.stringify({ body: newComment.value.trim() })
      }
    );

    if (res.status === 401 || res.status === 403) {
      throw new Error("You are not allowed to comment.");
    }
    if (!res.ok) {
      const text = await res.text().catch(() => "");
      throw new Error(text || `Failed to post comment (${res.status})`);
    }

    // Server returns the full updated recipe
    recipe.value = await res.json();
    newComment.value = "";
  } catch (e) {
    postError.value = e.message || "Unknown error";
  } finally {
    posting.value = false;
  }
}

// Submit rating
async function submitRating() {
  ratingError.value = "";
  ratingPosting.value = true;
  try {
    const res = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/recipes/${route.params.id}/ratings`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          ...(token ? { Authorization: `Bearer ${token}` } : {})
        },
        body: JSON.stringify({ score: Number(selectedRating.value) })
      }
    );

    if (res.status === 401 || res.status === 403) {
      throw new Error("You are not allowed to rate.");
    }
    if (![200, 201].includes(res.status)) {
      const text = await res.text().catch(() => "");
      throw new Error(text || `Failed to submit rating (${res.status})`);
    }

    // Server returns the full, updated recipe
    recipe.value = await res.json();
    preloadUserRating();
  } catch (e) {
    ratingError.value = e.message || "Unknown error";
  } finally {
    ratingPosting.value = false;
  }
}
</script>

<style scoped>
.page{padding:1rem 2rem}

.breadcrumb {
  font-size: .9rem;
  color: #666;
  margin-bottom: .75rem
}

.recipe-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, .05);
  padding: 1.25rem
}

.recipe-title {
  margin: .25rem 0;
  color: #222;
  font-size: 1.6rem
}

.badge {
  display: inline-block;
  margin-top: .5rem;
  background: #eef5ff;
  border: 1px solid #d0e0f7;
  color: #004a99;
  border-radius: 999px;
  padding: .2rem .6rem;
  font-size: .85rem
}

.meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin: 1rem 0;
  color: #666;
  font-size: .95rem
}

.section {
  margin-top: 2rem
}

.section h3 {
  margin: .2rem 0 .8rem 0;
  font-size: 1.1rem;
  color: #222
}

.desc {
  line-height: 1.5
}

.list {
  background: #fcfcfc;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: .5rem .75rem
}

ol.steps, ul.ingredients {
  margin: .2rem 0 .2rem 1rem;
  padding: 0
}

li {
  margin-bottom: .5rem
}

.comments {
  display: grid;
  gap: .75rem
}

.comment {
  border: 1px solid #eee;
  background: #fff;
  border-radius: 8px;
  padding: .75rem 1rem
}

.comment .head {
  display: flex;
  justify-content: space-between;
  gap: .75rem;
  color: #555;
  font-size: .9rem
}

.comment .body {
  margin-top: .35rem;
  line-height: 1.45
}

.muted {
  color: #888
}

a.link {
  color: #0056b3;
  text-decoration: none
}

a.link:hover {
  text-decoration: underline
}

/* Comment form */
.comment-form{display:grid;gap:.6rem;background:#f8fbff;border:1px solid #dfeafd;border-radius:10px;padding:1rem}
.comment-form textarea{min-height:90px;resize:vertical;padding:.6rem;border:1px solid #cfd6e4;border-radius:8px;font:inherit}
.comment-actions{display:flex;gap:.6rem;align-items:center;justify-content:space-between;flex-wrap:wrap}
.btn{background:#007bff;color:#fff;border:none;padding:.55rem 1rem;border-radius:10px;cursor:pointer;font:inherit}
.btn:hover{background:#0056b3}
.muted{color:#888}

/* Your rating */
.rate{
  display:grid;
  gap:.6rem;
  background:#f8fbff;
  border:1px solid #dfeafd;
  border-radius:10px;
  padding:1rem
}

.rate .stars{
  display:flex;
  flex-wrap:wrap;
  gap:.5rem
}

/* hide native radios, keep accessible */
.rate .stars input[type="radio"]{
  position:absolute;
  opacity:0;
  width:1px;height:1px;
  pointer-events:none
}

/* pill-like options */
.rate .stars label{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:2.2rem;
  padding:.35rem .7rem;
  border:1px solid #cfd6e4;
  border-radius:999px;
  background:#fff;
  color:#222;
  font-size:.95rem;
  cursor:pointer;
  transition:background .15s ease, border-color .15s ease, color .15s ease
}

/* hover/focus state */
.rate .stars label:hover,
.rate .stars label:focus-within{
  background:#eef5ff;
  border-color:#d0e0f7;
  color:#004a99
}

/* selected (add .is-selected via Vue for a strong state) */
.rate .stars label.is-selected{
  background:#007bff;
  border-color:#0056b3;
  color:#fff
}

/* disabled while posting */
.rate[aria-busy="true"] .stars label,
.rate .stars input[disabled] + label{
  opacity:.6;
  cursor:not-allowed
}

/* inline helper/error under the form */
.rate .help{color:#666;font-size:.9rem}
.rate .error{color:#b00020;font-size:.9rem}
</style>
