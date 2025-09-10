<template>
  <main>
    <h1>{{ recipe.title }}</h1>
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
    { id: 13, name: "Ingredient 1 for Pasta Aglio e Olio" },
    { id: 14, name: "Ingredient 2 for Pasta Aglio e Olio" },
    { id: 15, name: "Ingredient 3 for Pasta Aglio e Olio" },
    { id: 16, name: "Ingredient 4 for Pasta Aglio e Olio" }
  ],
  steps: [
    { id: 13, position: 1, instruction: "Step 1 instruction for Pasta Aglio e Olio" },
    { id: 14, position: 2, instruction: "Step 2 instruction for Pasta Aglio e Olio" },
    { id: 15, position: 3, instruction: "Step 3 instruction for Pasta Aglio e Olio" },
    { id: 16, position: 4, instruction: "Step 4 instruction for Pasta Aglio e Olio" }
  ],
  comments: [
    {
      id: 5,
      body: "Great and quick!",
      createdAt: "2025-09-07T15:36:56+00:00",
      author: { displayName: "Admin User" }
    },
    {
      id: 6,
      body: "Loved the simplicity.",
      createdAt: "2025-09-07T15:36:56+00:00",
      author: { displayName: "Regular User" }
    }
  ],
  author: { displayName: "Admin User" },
  averageRating: 4.5
};
</script>
