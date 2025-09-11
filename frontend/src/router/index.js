import { createRouter, createWebHistory } from 'vue-router'
import LoginView from "@/Views/LoginView.vue";
import HomeView from "@/Views/HomeView.vue";
import RecipeView from "@/Views/RecipeView.vue";
import RecipeCreateView from "@/Views/RecipeCreateView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/recipe/new',
      name: 'RecipeNew',
      component: RecipeCreateView,
    },
    {
      path: '/recipe/:id',
      name: 'recipe',
      component: RecipeView,
    },
  ],
})

export default router
