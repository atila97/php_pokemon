import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  linkActiveClass: 'active',
  history: createWebHistory(),
  routes: [
    {
      path: '/app',
      component:() => import('./components/Layout.vue'),
      children: [
        {
          path: '',
          name: 'home',
          component:  () => import('./views/home.vue'),
        },
        {
          path: 'login',
          name: 'login',
          component:  () => import('./views/login.vue'),
        },
        {
          path: 'register',
          name: 'register',
          component:  () => import('./views/register.vue'),
        },
        {
          path: 'pokemon/:id',
          name: 'pokemonDetails',
          component:  () => import('./views/pokemon-details.vue'),
        },
        {
          path: 'pokemon/:id/edit',
          name: 'pokemonEdit',
          component:  () => import('./views/PokemonEdit.vue'),
        },
      ]
    },
  ]
})

export default router
