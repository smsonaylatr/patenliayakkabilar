<script setup>
defineProps({
  hero: { type: Object, required: true },
});
</script>

<template>
  <section class="shopify-section shopify-section--slideshow">
    <style>
      #ded-vue-hero {
        --slideshow-background: linear-gradient(60deg, rgba(60, 65, 68, 1), rgba(23, 29, 33, 1) 98%);
        margin-block-start: calc(-1 * var(--header-height) * var(--section-is-first));
      }
    </style>
    <div id="ded-vue-hero" class="slideshow">
      <div class="slideshow__carousel" role="region">
        <div class="slideshow__slide is-selected" role="group">
          <div
            class="content-over-media content-over-media--auto"
            style="--content-over-media-content-max-width: 780px; --content-over-media-overlay: 0 0 0 / 0.1"
          >
            <img
              v-if="hero.imageDesktop"
              :src="hero.imageDesktop"
              alt=""
              loading="eager"
              fetchpriority="high"
              sizes="100vw"
              class="hidden sm:block"
            />
            <img
              v-if="hero.imageMobile"
              :src="hero.imageMobile"
              alt=""
              loading="eager"
              fetchpriority="high"
              sizes="100vw"
              class="sm:hidden"
            />
            <div
              class="place-self-center text-center sm:place-self-center sm:text-center text-custom"
              style="--text-color: 255 255 255"
            >
              <div class="prose">
                <p v-if="hero.subheading" class="bold">{{ hero.subheading }}</p>
                <p v-if="hero.heading" class="h4">{{ hero.heading }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
