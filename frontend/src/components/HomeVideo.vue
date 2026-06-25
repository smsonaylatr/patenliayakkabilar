<script setup>
import { computed } from 'vue';

const props = defineProps({
  video: { type: Object, required: true },
});

const embedUrl = computed(() => {
  const id = props.video?.youtubeId;
  if (!id) {
    return '';
  }
  const origin = encodeURIComponent(window.location.origin);
  return `https://www.youtube.com/embed/${id}?playsinline=1&autoplay=1&controls=0&mute=1&loop=1&playlist=${id}&enablejsapi=1&rel=0&modestbranding=1&origin=${origin}`;
});
</script>

<template>
  <section v-if="video.youtubeId" class="shopify-section shopify-section--video">
    <div class="section section-blends section-full text-custom" style="--text-color: 255 255 255">
      <div class="content-over-media aspect-video full-bleed text-custom" style="--text-color: 255 255 255">
        <iframe
          :src="embedUrl"
          allow="autoplay; encrypted-media"
          allowfullscreen
          class="pointer-events-none"
          style="width: 100%; height: 100%; border: 0"
          title="Video"
        />
        <div v-if="video.heading" class="place-self-center text-center">
          <div class="prose">
            <p class="h1">{{ video.heading }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
