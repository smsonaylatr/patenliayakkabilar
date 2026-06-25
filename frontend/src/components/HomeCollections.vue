<script setup>
defineProps({
  intro: { type: Object, required: true },
  collections: { type: Array, default: () => [] },
});
</script>

<template>
  <section class="shopify-section shopify-section--collection-list">
    <div class="section section-blends section-full">
      <div class="section-stack">
        <section-header class="section-header">
          <div class="prose">
            <h2 class="h2">{{ intro.heading }}</h2>
            <p>{{ intro.subtext }}</p>
          </div>
          <a :href="intro.moreUrl" class="text-with-icon group">
            <span class="reversed-link">Daha Fazla</span>
            <span class="circle-chevron group-hover:colors">
              <svg role="presentation" focusable="false" width="5" height="8" class="icon icon-chevron-right-small reverse-icon" viewBox="0 0 5 8">
                <path d="m.75 7 3-3-3-3" fill="none" stroke="currentColor" stroke-width="1.5" />
              </svg>
            </span>
          </a>
        </section-header>
        <div class="floating-controls-container">
          <div class="scroll-area bleed">
            <collection-list class="collection-list">
              <a
                v-for="col in collections"
                :key="col.id"
                :href="col.url"
                class="collection-card shadow"
              >
                <div class="content-over-media group rounded-sm" style="--content-over-media-overlay: 0 0 0 / 0.3">
                  <img
                    v-if="col.image"
                    :src="col.image"
                    :alt="col.title"
                    loading="lazy"
                    width="1230"
                    height="1230"
                    sizes="(max-width: 699px) 73vw, 533px"
                    class="zoom-image"
                  />
                  <div
                    class="collection-card__content-wrapper text-custom place-self-center text-center"
                    style="--text-color: 255 255 255"
                  >
                    <div class="collection-card__content prose">
                      <p class="h2">{{ col.title }}</p>
                    </div>
                    <svg role="presentation" focusable="false" width="40" height="40" class="icon icon-circle-button-right-clipped" viewBox="0 0 24 24">
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M12 24c6.627 0 12-5.373 12-12S18.627 0 12 0 0 5.373 0 12s5.373 12 12 12ZM10.47 9.53 12.94 12l-2.47 2.47 1.06 1.06 3-3 .53-.53-.53-.53-3-3-1.06 1.06Z"
                        fill="currentColor"
                      />
                    </svg>
                  </div>
                </div>
              </a>
            </collection-list>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
