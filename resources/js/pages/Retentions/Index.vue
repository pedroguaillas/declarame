<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { importMethod } from '@/actions/App/Http/Controllers/Tenant/RetentionController';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Retention {
  id: number;
  code: string;
  type: string;
  description: string;
  percentage: number;
}

defineProps<{
  retentions: Retention[];
}>();

const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm<{ file: File | null }>({ file: null });

function openFilePicker() {
  fileInput.value?.click();
}

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0] ?? null;

  if (!file) {
    return;
  }

  form.file = file;
  form.post(importMethod.url(), { forceFormData: true });
}
</script>

<template>
  <Head title="Retenciones" />

  <AdminLayout>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-foreground text-2xl font-semibold">Retenciones</h1>

      <div class="flex items-center gap-3">
        <span v-if="form.processing" class="text-muted-foreground text-sm">Importando…</span>

        <button
          v-if="retentions.length > 0"
          type="button"
          class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors disabled:opacity-60"
          :disabled="form.processing"
          @click="openFilePicker"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[16px]">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
          </svg>
          Reimportar
        </button>

        <input
          ref="fileInput"
          type="file"
          accept=".dat,.xml"
          class="hidden"
          @change="handleFileChange"
        />
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-if="retentions.length === 0"
      class="border-border bg-card flex flex-col items-center justify-center rounded-lg border py-16"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-muted-foreground mb-4 size-12">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
      </svg>
      <p class="text-foreground mb-1 text-sm font-medium">No hay retenciones registradas</p>
      <p class="text-muted-foreground mb-6 text-sm">Importa la tabla de retenciones desde un archivo .dat</p>
      <button
        type="button"
        class="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-2 rounded-md px-5 py-2.5 text-sm font-medium transition-colors disabled:opacity-60"
        :disabled="form.processing"
        @click="openFilePicker"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[16px]">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
        </svg>
        Importar Retenciones
      </button>
    </div>

    <!-- Table -->
    <div v-else class="border-border bg-card overflow-hidden rounded-lg border">
      <table class="divide-border min-w-full divide-y">
        <thead class="bg-muted">
          <tr>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Código</th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Tipo</th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Descripción</th>
            <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">% Retención</th>
          </tr>
        </thead>
        <tbody class="divide-border bg-card divide-y">
          <tr
            v-for="retention in retentions"
            :key="retention.id"
            class="hover:bg-muted/40 transition-colors"
          >
            <td class="text-foreground px-5 py-3 font-mono text-sm font-medium whitespace-nowrap">
              {{ retention.code }}
            </td>
            <td class="px-5 py-3 text-sm whitespace-nowrap">
              <span
                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                :class="retention.type === 'iva'
                  ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                  : 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'"
              >
                {{ retention.type.toUpperCase() }}
              </span>
            </td>
            <td class="text-muted-foreground px-5 py-3 text-sm">
              {{ retention.description }}
            </td>
            <td class="text-foreground px-5 py-3 text-right font-mono text-sm whitespace-nowrap">
              {{ Number(retention.percentage).toFixed(2) }}%
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
