import { createRouter, createWebHistory } from 'vue-router';
import Index from "./views/Index.vue";
import Sano from "./views/Sana.vue";
import Sana from "./views/Sano.vue";

const routes = [
    { path: '/', name: 'Index', component: Index },
    { path: '/color/sano', name: 'Sano', component: Sano },
    { path: '/color/sana', name: 'Sana', component: Sana }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router;
