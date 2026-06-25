<script setup>
defineProps({
  product: { type: Object, required: true },
});
</script>

<template>
  <div
    class="product-card product-card--blends product-card--show-secondary-media bg-custom text-custom"
    style="--background: 255 255 255; --text-color: 26 26 26"
  >
    <div v-if="product.onSale && product.savingLabel" class="product-card__badge-list">
      <span class="badge badge--on-sale">{{ product.savingLabel }}</span>
    </div>
    <div v-else-if="product.audience" class="product-card__badge-list">
      <span class="badge badge--audience">{{ product.audience }}</span>
    </div>
    <div class="product-card__figure">
      <a :href="product.url">
        <img
          v-if="product.imagePrimary"
          :src="product.imagePrimary"
          :alt="product.title"
          loading="lazy"
          width="1230"
          height="1230"
          class="product-card__image product-card__image--primary aspect-natural"
        />
        <img
          v-if="product.imageSecondary"
          :src="product.imageSecondary"
          :alt="product.title"
          loading="lazy"
          class="product-card__image product-card__image--secondary object-fill"
        />
      </a>
    </div>
    <div class="product-card__info">
      <div class="v-stack gap-0.5 w-full">
        <span class="product-card__title">
          <a :href="product.url" class="bold">{{ product.title }}</a>
        </span>
        <div class="price-list">
          <sale-price :class="{ 'text-on-sale': product.onSale }">
            <span class="sr-only">{{ product.onSale ? 'İndirimli fiyat' : 'Fiyat' }}</span>
            {{ product.priceFormatted }}
          </sale-price>
          <compare-at-price v-if="product.onSale && product.compareAtFormatted" class="text-subdued line-through">
            <span class="sr-only">Normal fiyat</span>
            {{ product.compareAtFormatted }}
          </compare-at-price>
        </div>
      </div>
    </div>
  </div>
</template>
