import { createRouter, createWebHistory } from 'vue-router';
import Login from './Pages/Login.vue';
import Dashboard from './Pages/Dashboard.vue';
import Register from './Pages/Register.vue';

const routes = [
    { path: '/', component: Login },
    { path: '/dashboard', component: Dashboard },
    { path: '/register', component: Register },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
