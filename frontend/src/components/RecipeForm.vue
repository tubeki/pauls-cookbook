<script setup>
import { reactive } from 'vue'

const emit = defineEmits(['submit', 'cancel'])

const form = reactive({
  title: '',
  description: '',
  ingredients: [{ name: '' }],
  steps: [{ instruction: '' }]
})

function addIngredient() {
  form.ingredients.push({ name: '' })
}
function removeIngredient(index) {
  form.ingredients.splice(index, 1)
}

function addStep() {
  form.steps.push({ instruction: '' })
}
function removeStep(index) {
  form.steps.splice(index, 1)
}

function handleSubmit() {
  const payload = {
    title: form.title,
    description: form.description || null,
    ingredients: form.ingredients.map(i => ({ name: i.name })),
    steps: form.steps.map((s, idx) => ({
      position: idx + 1,
      instruction: s.instruction
    }))
  }
  emit('submit', payload)
}
</script>

<template>
  <form class="form" @submit.prevent="handleSubmit">
    <section class="section">
      <h3>Basics</h3>
      <label class="label">Title</label>
      <input v-model="form.title" type="text" class="input" required />

      <label class="label">Description</label>
      <textarea v-model="form.description" class="textarea" rows="4" required></textarea>
    </section>

    <section class="section">
      <h3>Ingredients</h3>
      <div class="list">
        <div class="row" v-for="(ing, i) in form.ingredients" :key="i">
          <input v-model="ing.name" type="text" class="input flex" required />
          <button
            type="button"
            class="btn btn-danger"
            @click="removeIngredient(i)"
            v-if="form.ingredients.length > 1"
            aria-label="Remove ingredient"
          >
            Remove
          </button>
        </div>
      </div>
      <button type="button" class="btn btn-outline" @click="addIngredient">Add ingredient</button>
    </section>

    <section class="section">
      <h3>Steps</h3>
      <div class="list">
        <div class="row" v-for="(st, i) in form.steps" :key="i">
          <textarea v-model="st.instruction" class="textarea flex" rows="3" required></textarea>
          <button
            type="button"
            class="btn btn-danger"
            @click="removeStep(i)"
            v-if="form.steps.length > 1"
            aria-label="Remove step"
          >
            Remove
          </button>
        </div>
      </div>
      <button type="button" class="btn btn-outline" @click="addStep">Add step</button>
    </section>

    <div class="actions">
      <button type="submit" class="btn">Save Recipe</button>
      <button type="button" class="btn btn-muted" @click="$emit('cancel')">Cancel</button>
    </div>
  </form>
</template>

<style scoped>
.form{display:grid;gap:1.25rem}
.section{margin-top:.5rem}
.section h3{margin:.2rem 0 .8rem 0;font-size:1.1rem;color:#222}

.label{display:block;margin:.25rem 0 .35rem 0;color:#333;font-weight:600}

.input,.textarea{
  width:100%;
  box-sizing:border-box;   /* ✅ prevents overflow */
  padding:.6rem .7rem;
  border:1px solid #cfd6e4;
  border-radius:8px;
  background:#fff;
  font:inherit
}
.textarea{min-height:90px;resize:vertical}

.list{
  background:#fcfcfc;
  border:1px solid #eee;
  border-radius:8px;
  padding:.6rem .75rem;
  display:grid;gap:.6rem
}
.row{display:flex;gap:.5rem;align-items:flex-start}
.flex{flex:1}

.actions{display:flex;gap:.6rem;flex-wrap:wrap;margin-top:.5rem}

.btn{
  background:#007bff;
  color:#fff;
  border:1px solid #007bff;
  padding:.55rem 1rem;
  border-radius:10px;
  cursor:pointer;
  font:inherit
}
.btn:hover{background:#0056b3;border-color:#0056b3}

.btn-outline{
  background:#fff;
  color:#004a99;
  border:1px solid #d0e0f7
}
.btn-outline:hover{background:#eef5ff;border-color:#c4d8ff}

.btn-muted{
  background:#f3f4f6;
  color:#333;
  border:1px solid #e5e7eb
}
.btn-muted:hover{background:#e8eaee}

.btn-danger{
  background:#fff5f5;
  color:#8a1420;
  border:1px solid #f3c0c6
}
.btn-danger:hover{background:#ffe9ea;border-color:#e8aab1}
</style>

