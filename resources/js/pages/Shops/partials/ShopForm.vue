<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Tenant/ShopController';
import SelectField from '@/components/ui/select/SelectField.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentCompany = computed(() => (page.props as any).currentCompany ?? null);

interface Option {
  value: number;
  label: string;
}

interface FormErrors {
  [key: string]: string | undefined;
}

const props = defineProps<{
  form: {
    contact_id: number | null;
    voucher_type: number | string;
    emision: string;
    autorization: string;
    autorized_at: string;
    serie: string;
    sub_total: number | string;
    no_iva: number | string;
    base0: number | string;
    base12: number | string;
    base15: number | string;
    iva12: number | string;
    iva15: number | string;
    aditional_discount: number | string;
    discount: number | string;
    ice: number | string;
    total: number | string;
    state: string;
    serie_retention: string;
    date_retention: string;
    state_retention: string;
    autorization_retention: string;
    errors: FormErrors;
    processing: boolean;
  };
  contacts: Option[];
  submitLabel: string;
}>();

const emit = defineEmits<{
  submit: [];
}>();

const voucherTypes = [
  { value: 1, label: '01 - Factura' },
  { value: 2, label: '02 - Nota de Venta' },
  { value: 3, label: '03 - Liquidación de Compras' },
  { value: 4, label: '04 - Nota de Crédito' },
  { value: 5, label: '05 - Nota de Débito' },
  { value: 6, label: '06 - Guía de Remisión' },
  { value: 7, label: '07 - Comprobante de Retención' },
];

const stateOptions = [
  { value: 'AUTORIZADO', label: 'Autorizado' },
  { value: 'PENDIENTE', label: 'Pendiente' },
  { value: 'ANULADO', label: 'Anulado' },
];

const stateRetentionOptions = [
  { value: 'AUTORIZADO', label: 'Autorizado' },
  { value: 'PENDIENTE', label: 'Pendiente' },
  { value: 'ANULADO', label: 'Anulado' },
];
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <!-- Sección: Documento -->
    <div class="p-6">
      <h2
        class="text-muted-foreground mb-4 text-xs font-semibold tracking-widest uppercase"
      >
        Documento
      </h2>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div>
          <label class="text-muted-foreground mb-1.5 block text-sm font-medium">
            Empresa
          </label>
          <div
            v-if="currentCompany"
            class="border-border bg-muted text-foreground flex h-9 items-center rounded-md border px-3 text-sm font-medium"
          >
            {{ currentCompany.name }}
          </div>
          <div
            v-else
            class="border-border bg-muted text-muted-foreground flex h-9 items-center rounded-md border px-3 text-sm"
          >
            Sin empresa seleccionada
          </div>
        </div>

        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium">
            Proveedor / Contacto <span class="text-destructive">*</span>
          </label>
          <SelectField
            :model-value="form.contact_id"
            :options="contacts"
            placeholder="Seleccionar contacto"
            @update:model-value="(v) => (form.contact_id = Number(v))"
          />
          <p
            v-if="form.errors.contact_id"
            class="text-destructive mt-1 text-xs"
          >
            {{ form.errors.contact_id }}
          </p>
        </div>

        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium">
            Tipo de comprobante <span class="text-destructive">*</span>
          </label>
          <SelectField
            :model-value="form.voucher_type"
            :options="voucherTypes"
            placeholder="Seleccionar tipo"
            @update:model-value="(v) => (form.voucher_type = Number(v))"
          />
          <p
            v-if="form.errors.voucher_type"
            class="text-destructive mt-1 text-xs"
          >
            {{ form.errors.voucher_type }}
          </p>
        </div>

        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium">
            Serie <span class="text-destructive">*</span>
          </label>
          <input
            v-model="form.serie"
            type="text"
            maxlength="17"
            placeholder="001-001-000000001"
            class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 font-mono text-sm focus:ring-2 focus:outline-none"
          />
          <p v-if="form.errors.serie" class="text-destructive mt-1 text-xs">
            {{ form.errors.serie }}
          </p>
        </div>

        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium">
            Fecha de emisión <span class="text-destructive">*</span>
          </label>
          <input
            v-model="form.emision"
            type="date"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:outline-none"
          />
          <p v-if="form.errors.emision" class="text-destructive mt-1 text-xs">
            {{ form.errors.emision }}
          </p>
        </div>

        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium">
            Estado <span class="text-destructive">*</span>
          </label>
          <SelectField
            :model-value="form.state"
            :options="stateOptions"
            placeholder="Seleccionar estado"
            @update:model-value="(v) => (form.state = String(v))"
          />
          <p v-if="form.errors.state" class="text-destructive mt-1 text-xs">
            {{ form.errors.state }}
          </p>
        </div>

        <div class="lg:col-span-2">
          <label class="text-foreground mb-1.5 block text-sm font-medium">
            Autorización <span class="text-destructive">*</span>
          </label>
          <input
            v-model="form.autorization"
            type="text"
            maxlength="49"
            class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 font-mono text-sm focus:ring-2 focus:outline-none"
          />
          <p
            v-if="form.errors.autorization"
            class="text-destructive mt-1 text-xs"
          >
            {{ form.errors.autorization }}
          </p>
        </div>

        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Fecha de autorización</label
          >
          <input
            v-model="form.autorized_at"
            type="datetime-local"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:outline-none"
          />
          <p
            v-if="form.errors.autorized_at"
            class="text-destructive mt-1 text-xs"
          >
            {{ form.errors.autorized_at }}
          </p>
        </div>
      </div>
    </div>

    <!-- Sección: Valores -->
    <div class="border-border border-t p-6">
      <h2
        class="text-muted-foreground mb-4 text-xs font-semibold tracking-widest uppercase"
      >
        Valores
      </h2>
      <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Subtotal <span class="text-destructive">*</span></label
          >
          <input
            v-model="form.sub_total"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
          <p v-if="form.errors.sub_total" class="text-destructive mt-1 text-xs">
            {{ form.errors.sub_total }}
          </p>
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >No IVA</label
          >
          <input
            v-model="form.no_iva"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Base 0%</label
          >
          <input
            v-model="form.base0"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Base 12%</label
          >
          <input
            v-model="form.base12"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >IVA 12%</label
          >
          <input
            v-model="form.iva12"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Base 15%</label
          >
          <input
            v-model="form.base15"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >IVA 15%</label
          >
          <input
            v-model="form.iva15"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Descuento</label
          >
          <input
            v-model="form.discount"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Desc. adicional</label
          >
          <input
            v-model="form.aditional_discount"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >ICE</label
          >
          <input
            v-model="form.ice"
            type="number"
            step="0.01"
            min="0"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-right font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div class="bg-muted rounded-lg px-4 py-3">
          <label
            class="text-muted-foreground mb-1 block text-xs font-semibold tracking-wider uppercase"
            >Total <span class="text-destructive">*</span></label
          >
          <input
            v-model="form.total"
            type="number"
            step="0.01"
            min="0"
            class="text-foreground block w-full bg-transparent text-right font-mono text-base font-semibold focus:outline-none"
          />
          <p v-if="form.errors.total" class="text-destructive mt-1 text-xs">
            {{ form.errors.total }}
          </p>
        </div>
      </div>
    </div>

    <!-- Sección: Retención -->
    <div class="border-border border-t p-6">
      <h2
        class="text-muted-foreground mb-4 text-xs font-semibold tracking-widest uppercase"
      >
        Retención
      </h2>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Serie retención</label
          >
          <input
            v-model="form.serie_retention"
            type="text"
            maxlength="17"
            class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Fecha retención</label
          >
          <input
            v-model="form.date_retention"
            type="date"
            class="border-border bg-background text-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:outline-none"
          />
        </div>
        <div>
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Estado retención</label
          >
          <SelectField
            :model-value="form.state_retention"
            :options="stateRetentionOptions"
            placeholder="Seleccionar estado"
            @update:model-value="(v) => (form.state_retention = String(v))"
          />
        </div>
        <div class="sm:col-span-2">
          <label class="text-foreground mb-1.5 block text-sm font-medium"
            >Autorización retención</label
          >
          <input
            v-model="form.autorization_retention"
            type="text"
            maxlength="49"
            class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/30 block w-full rounded-md border px-3 py-2 font-mono text-sm focus:ring-2 focus:outline-none"
          />
        </div>
      </div>
    </div>

    <!-- Acciones -->
    <div
      class="border-border flex items-center justify-end gap-3 border-t px-6 py-4"
    >
      <Link
        :href="index.url()"
        class="border-border text-foreground hover:bg-accent rounded-md border px-4 py-2 text-sm font-medium transition-colors"
      >
        Cancelar
      </Link>
      <button
        type="submit"
        :disabled="form.processing"
        class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium transition-colors disabled:opacity-50"
      >
        {{ form.processing ? 'Guardando...' : submitLabel }}
      </button>
    </div>
  </form>
</template>
