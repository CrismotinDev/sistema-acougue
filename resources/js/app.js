import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import vuetify from './plugins/vuetify';
import router from './router'; // Importe o router

const app = createApp(App);
app.use(vuetify);
app.use(router); // Use o router
app.mount('#app');
