<script setup>
defineProps({
  block: { type: Object, required: true },
});
</script>

<template>
  <section class="shopify-section shopify-section--rich-text">
    <div class="section section-blends section-full">
      <div class="rich-text justify-center">
        <div class="rich-text__wrapper">
          <div class="prose justify-items-center text-center">
            <p v-if="block.heading" class="h2 hyphenate">{{ block.heading }}</p>
            <div v-if="block.bodyHtml" v-html="block.bodyHtml" />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
