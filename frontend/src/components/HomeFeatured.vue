<script setup>
import HomeProductCard from './HomeProductCard.vue';

defineProps({
  block: { type: Object, required: true },
  sectionId: { type: String, required: true },
});
</script>

<template>
  <section :id="sectionId" class="shopify-section shopify-section--featured-collection">
    <style>
      .vue-featured .product-list {
        --product-list-gap: var(--product-list-row-gap) var(--spacing-2);
        --product-list-items-per-row: 2;
        --product-list-carousel-item-width: 74vw;
        --product-list-grid: auto / repeat(var(--product-list-items-per-row), minmax(0, 1fr));
      }
      @media screen and (min-width: 700px) {
        .vue-featured .product-list {
          --product-list-gap: var(--product-list-row-gap) var(--product-list-column-gap);
          --product-list-items-per-row: 2;
          --product-list-carousel-item-width: 36vw;
        }
      }
      @media screen and (min-width: 1000px) {
        .vue-featured .product-list {
          --product-list-items-per-row: 4;
        }
      }
    </style>
    <div class="section section-blends section-full vue-featured">
      <div class="section-stack">
        <section-header class="section-header">
          <div class="prose">
            <h2 class="h2">{{ block.title }}</h2>
          </div>
          <a :href="block.collectionUrl" class="text-with-icon group">
            <span class="reversed-link">Daha Fazla</span>
            <span class="circle-chevron group-hover:colors">
              <svg role="presentation" focusable="false" width="5" height="8" class="icon icon-chevron-right-small reverse-icon" viewBox="0 0 5 8">
                <path d="m.75 7 3-3-3-3" fill="none" stroke="currentColor" stroke-width="1.5" />
              </svg>
            </span>
          </a>
        </section-header>
        <div class="floating-controls-container">
          <div class="scroll-area bleed is-scrollable">
            <product-list v-if="block.products?.length" class="product-list">
              <HomeProductCard v-for="p in block.products" :key="p.slug" :product="p" />
            </product-list>
            <p v-else class="text-subdued" style="padding: 1rem 0">Bu bölümde henüz ürün yok.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
