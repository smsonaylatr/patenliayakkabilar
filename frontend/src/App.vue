<script setup>
import { onMounted } from 'vue';
import { useHomeData } from './composables/useHomeData.js';
import HomeHero from './components/HomeHero.vue';
import HomeCollections from './components/HomeCollections.vue';
import HomeScrollingText from './components/HomeScrollingText.vue';
import HomeFeatured from './components/HomeFeatured.vue';
import HomeVideo from './components/HomeVideo.vue';
import HomeImageText from './components/HomeImageText.vue';
import HomeRichText from './components/HomeRichText.vue';
import HomeImageOverlay from './components/HomeImageOverlay.vue';

const { home, loading, error, load } = useHomeData();
onMounted(load);
</script>

<template>
  <div v-if="loading" class="section section-blends section-full" style="padding: 4rem 1rem; text-align: center">
    <p class="h4">Yükleniyor…</p>
  </div>
  <div v-else-if="error" class="section section-blends section-full" style="padding: 4rem 1rem; text-align: center">
    <p class="h4">{{ error }}</p>
  </div>
  <template v-else-if="home">
    <HomeHero :hero="home.hero" />
    <HomeCollections :intro="home.collectionsIntro" :collections="home.collections" />
    <HomeScrollingText :text="home.scrollingText" />
    <HomeFeatured
      v-for="(block, i) in home.featured"
      :key="i"
      :block="block"
      :section-id="`vue-featured-${i}`"
    />
    <HomeVideo :video="home.video" />
    <HomeImageText :block="home.imageText" />
    <HomeRichText :block="home.richText" />
    <HomeImageOverlay :block="home.imageOverlay" />
  </template>
</template>
