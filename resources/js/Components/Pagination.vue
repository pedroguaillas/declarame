<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Link {
  url: string | null;
  label: string;
  active: boolean;
}

const props = defineProps<{
  from: number | null;
  to: number | null;
  total: number;
  prev_page_url: string | null;
  next_page_url: string | null;
  links: Link[];
}>();

// Strip the first (Previous) and last (Next) entries — we render our own
const pageLinks = computed(() => props.links.slice(1, -1));

function navigate(url: string | null) {
  if (!url) return;
  router.get(url, {}, { preserveScroll: true, preserveState: true });
}
</script>

<template>
  <div class="flex items-center justify-between px-5 py-3">
    <!-- Count info -->
    <p class="text-muted-foreground text-sm">
      <template v-if="total === 0">Sin resultados</template>
      <template v-else>
        Mostrando {{ from }}–{{ to }} de {{ total }}
      </template>
    </p>

    <!-- Controls -->
    <div class="flex items-center gap-1">
      <!-- Previous -->
      <button
        type="button"
        :disabled="!prev_page_url"
        class="rounded-md p-1.5 transition-colors"
        :class="prev_page_url ? 'text-foreground hover:bg-muted' : 'text-muted-foreground cursor-not-allowed opacity-40'"
        @click="navigate(prev_page_url)"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
      </button>

      <!-- Page numbers -->
      <template v-for="link in pageLinks" :key="link.label">
        <span v-if="link.label === '...'" class="text-muted-foreground px-1 text-sm">…</span>
        <button
          v-else
          type="button"
          class="min-w-[2rem] rounded-md px-1.5 py-1 text-sm font-medium transition-colors"
          :class="link.active
            ? 'bg-primary text-primary-foreground'
            : 'text-foreground hover:bg-muted'"
          @click="navigate(link.url)"
        >
          {{ link.label }}
        </button>
      </template>

      <!-- Next -->
      <button
        type="button"
        :disabled="!next_page_url"
        class="rounded-md p-1.5 transition-colors"
        :class="next_page_url ? 'text-foreground hover:bg-muted' : 'text-muted-foreground cursor-not-allowed opacity-40'"
        @click="navigate(next_page_url)"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
      </button>
    </div>
  </div>
</template>
