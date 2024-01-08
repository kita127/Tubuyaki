import './bootstrap';

// import axios from 'axios';
// window.axios = axios;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import { createApp } from 'vue';
import App from "./components/App.vue"

const app = createApp(App);

console.log(app.version);

app.mount('#app');
