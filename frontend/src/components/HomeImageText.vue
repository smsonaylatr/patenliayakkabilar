<script setup>
defineProps({
  block: { type: Object, required: true },
});
</script>

<template>
  <section class="shopify-section shopify-section--multiple-images-with-text">
    <div class="section section--narrow section-blends section-full">
      <div class="multiple-images-with-text">
        <div class="multiple-images-with-text__image-list">
          <img
            v-if="block.image"
            :src="block.image"
            alt=""
            loading="lazy"
            sizes="min(100vw, 500px)"
            class="rounded-sm shadow-sm"
          />
        </div>
        <div class="multiple-images-with-text__content-with-nav">
          <div class="multiple-images-with-text__content-list">
            <div class="prose is-selected">
              <p v-if="block.heading" class="h1">{{ block.heading }}</p>
              <div v-if="block.bodyHtml" v-html="block.bodyHtml" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
