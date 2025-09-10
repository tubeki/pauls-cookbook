<template>
  <main class="page">
    <nav class="breadcrumb">
      <RouterLink class="link" to="/">Home</RouterLink> /
      <span class="muted">Recipes</span> /
      <span>{{ recipe.title }}</span>
    </nav>

    <article class="recipe-card">
      <h1 class="recipe-title">{{ recipe.title }}</h1>
      <div class="badge" v-if="recipe.averageRating">
        ⭐ {{ Number(recipe.averageRating).toFixed(1) }}
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
        <p class="muted" style="margin-top:.5rem">
          Want to leave a comment?
          <RouterLink class="link" to="/login">Sign in</RouterLink>.
        </p>
      </section>
    </article>
  </main>
</template>
<script setup>
const recipe = {
  id: 4,
  title: "Pasta Aglio e Olio",
  description: "Simple garlic & oil pasta.",
  createdAt: "2025-09-07T15:36:56+00:00",
  updatedAt: null,
  ingredients: [
    {id: 13, name: "Ingredient 1 for Pasta Aglio e Olio"},
    {id: 14, name: "Ingredient 2 for Pasta Aglio e Olio"},
    {id: 15, name: "Ingredient 3 for Pasta Aglio e Olio"},
    {id: 16, name: "Ingredient 4 for Pasta Aglio e Olio"}
  ],
  steps: [
    {id: 13, position: 1, instruction: "Step 1 instruction for Pasta Aglio e Olio"},
    {id: 14, position: 2, instruction: "Step 2 instruction for Pasta Aglio e Olio"},
    {id: 15, position: 3, instruction: "Step 3 instruction for Pasta Aglio e Olio"},
    {id: 16, position: 4, instruction: "Step 4 instruction for Pasta Aglio e Olio"}
  ],
  comments: [
    {
      id: 5,
      body: "Great and quick!",
      createdAt: "2025-09-07T15:36:56+00:00",
      author: {displayName: "Admin User"}
    },
    {
      id: 6,
      body: "Loved the simplicity.",
      createdAt: "2025-09-07T15:36:56+00:00",
      author: {displayName: "Regular User"}
    }
  ],
  author: {displayName: "Admin User"},
  averageRating: 4.5
};

function formatDate(iso) {
  if (!iso) return "";
  return new Intl.DateTimeFormat(undefined, {
    year: "numeric", month: "short", day: "numeric"
  }).format(new Date(iso));
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
</style>
