<script setup>
defineProps({
  block: { type: Object, required: true },
});
</script>

<template>
  <section class="shopify-section shopify-section--image-with-text-overlay">
    <div class="section section-blends section-full text-custom" style="--text-color: 255 255 255">
      <div class="content-over-media content-over-media--auto full-bleed text-custom" style="--text-color: 255 255 255">
        <img v-if="block.image" :src="block.image" alt="" loading="lazy" sizes="100vw" />
        <div v-if="block.heading" class="place-self-center text-center sm:place-self-center sm:text-center">
          <div class="prose">
            <p class="h1">{{ block.heading }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
