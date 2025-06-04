import { createRouter, createWebHistory } from 'vue-router'
import inicioLogin from '../components/inicioLogin.vue'
import Header from '../components/shared/Header.vue'

const routes = [
  {
    path: '/',
    name: 'login',
    component: inicioLogin
  },
  {
    path: '/productos',
    name: 'productos',
    components: {
      default: () => import('../components/listaProductos.vue'),
      header: Header
    }
  },
  {
  path: '/pedidos',
  name: 'pedidos',
  component: () => import('../components/Pedidos.vue')
},
{
  path: '/factura',
  name: 'factura',
  component: () => import('../components/Factura.vue')
},
{
  path: '/admin-productos',
  name: 'admin-productos',
  component: () => import('../components/AdminProductos.vue'),
  meta: { requiresAuth: true, requiredRole: 'Admin' } // Opcional: para control de acceso
}
  // Añade más rutas según avancemos (pedidos, factura, etc.)
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router