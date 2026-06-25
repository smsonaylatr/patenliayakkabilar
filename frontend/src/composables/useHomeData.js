import { ref, shallowRef } from 'vue';

function readBoot() {
  const node = document.getElementById('ded-vue-home-boot');
  if (!node?.textContent) {
    return null;
  }
  try {
    const data = JSON.parse(node.textContent);
    return data?.ok ? data.home : null;
  } catch {
    return null;
  }
}

export function useHomeData() {
  const home = shallowRef(readBoot());
  const loading = ref(!home.value);
  const error = ref('');

  async function load() {
    if (home.value) {
      loading.value = false;
      return;
    }
    const boot = readBoot();
    let apiUrl = boot?.urls?.api;
    if (!apiUrl) {
      const baseEl = document.querySelector('base');
      if (baseEl?.href) {
        apiUrl = new URL('api.php?path=public-home', baseEl.href).href;
      } else {
        const dir = window.location.pathname.replace(/\/[^/]*$/, '') || '';
        apiUrl = `${dir}/api.php?path=public-home`;
      }
    }
    try {
      const res = await fetch(apiUrl);
      const data = await res.json();
      if (!data?.ok || !data.home) {
        throw new Error(data?.error || 'home_load_failed');
      }
      home.value = data.home;
    } catch (e) {
      error.value = e?.message || 'Yüklenemedi';
    } finally {
      loading.value = false;
    }
  }

  return { home, loading, error, load };
}
