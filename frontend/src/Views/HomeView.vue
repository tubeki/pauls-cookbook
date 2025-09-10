<template>
  <div class="search-bar">
    <input type="search" placeholder="Search recipes..."/>
    <button type="button">Search</button>
  </div>

  <div class="recipe-list">
    <RouterLink
      v-for="r in recipes"
      :key="r.id"
      class="recipe"
      :to="`/recipe/${r.id}`"
    >
      <h2 class="recipe-title">{{ r.title }}</h2>
      <div class="meta">⭐ {{ r.averageRating.toFixed(1) }} | {{ r.commentCount }} comments</div>
    </RouterLink>
  </div>
</template>

<script setup>
const recipes = [
  {id: 4, title: "Pasta Aglio e Olio", averageRating: 4.5, commentCount: 2},
  {id: 5, title: "Tomato Soup", averageRating: 4.5, commentCount: 2},
  {id: 6, title: "Pancakes", averageRating: 0, commentCount: 0},
  {id: 7, title: "New Recipe", averageRating: 4.5, commentCount: 2},
  {id: 8, title: "TITITITITITIITITITIITT", averageRating: 0, commentCount: 0},
  {id: 9, title: "New Recipe 2", averageRating: 0, commentCount: 0}
];
</script>

<style scoped>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: #f9f9f9;
  color: #333;
}

.search-bar {
  display: flex;
  gap: 0.5rem;
  padding: 1rem 2rem;
  background: #fff;
  border-bottom: 1px solid #ddd;
}

.search-bar input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.search-bar button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}

.search-bar button:hover {
  background-color: #0056b3;
}

.recipe-list {
  padding: 1rem 2rem;
}

.recipe {
  display: block;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  text-decoration: none;
  color: inherit;
  transition: background 0.2s, transform 0.1s;
  cursor: pointer; /* pointer cursor */
}

.recipe:hover {
  background: #f0f7ff;
  transform: translateY(-2px);
}

.recipe h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.2rem;
  color: #222;
}

.recipe .meta {
  font-size: 0.9rem;
  color: #666;
}
</style>
