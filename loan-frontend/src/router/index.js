import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('@/views/Home.vue')
  },
  {
    path: '/new-application',
    name: 'NewApplication',
    component: () => import('@/views/NewApplication.vue')
  },
  {
    path: '/applications/:id',
    name: 'ApplicationDetails',
    component: () => import('@/views/ApplicationDetails.vue'),
    props: true
  },
  {
    path: '/history',
    name: 'ApplicationHistory',
    component: () => import('@/views/ApplicationHistory.vue')
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue')
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
