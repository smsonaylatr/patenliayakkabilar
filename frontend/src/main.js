import { createApp } from 'vue';
import App from './App.vue';

let app = null;

function mountVue() {
  const el = document.getElementById('ded-vue-home');
  if (el) {
    if (app) app.unmount();
    app = createApp(App);
    app.mount(el);
  } else if (app) {
    app.unmount();
    app = null;
  }
}

// Initial load
mountVue();

// Swup SPA support
document.addEventListener('swup:pageView', mountVue);
