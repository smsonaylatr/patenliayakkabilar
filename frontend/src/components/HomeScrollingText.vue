<script setup>
defineProps({
  text: { type: String, default: '' },
});
</script>

<template>
  <section v-if="text" class="shopify-section shopify-section--scrolling-text">
    <div class="section section--tight section-blends section-full">
      <div class="scrolling-text scrolling-text--auto">
        <div class="scrolling-text__wrapper">
          <span v-for="n in 10" :key="n" class="scrolling-text__text heading" :aria-hidden="n > 1 ? 'true' : undefined">
            {{ text }}
          </span>
        </div>
      </div>
    </div>
  </section>
</template>
