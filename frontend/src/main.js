import { createApp } from 'vue';
import App from './App.vue';

const el = document.getElementById('ded-vue-home');
if (el) {
  createApp(App).mount(el);
}
