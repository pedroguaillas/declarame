<script setup lang="ts">
import { resolve as resolveContact } from '@/actions/App/Http/Controllers/Tenant/ContactController';
import { index } from '@/actions/App/Http/Controllers/Tenant/ShopController';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import SelectField from '@/components/ui/select/SelectField.vue';
import { VoucherType } from '@/types/voucher-type';
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch, watchEffect } from 'vue';

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
    voucher_type_id: number | string;
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
  voucherTypes: VoucherType[];
  submitLabel: string;
}>();

const voucherTypeOptions: Option[] = props.voucherTypes.map((voucherType) => ({
  value: voucherType.id,
  label: `${voucherType.code} - ${voucherType.description}`,
}));

const contactIdentification = ref('');
const contactName = ref('');
const contactResolving = ref(false);
const contactError = ref<string | null>(null);

watch(contactIdentification, async (identification) => {
  if (identification.length !== 10 && identification.length !== 13) {
    contactName.value = '';
    contactError.value = null;
    props.form.contact_id = null;
    return;
  }

  contactResolving.value = true;
  contactError.value = null;

  try {
    const res = await fetch(resolveContact(identification).url, {
      headers: { Accept: 'application/json' },
    });

    if (!res.ok) {
      contactName.value = '';
      props.form.contact_id = null;
      contactError.value = 'No se encontró el contacto con esa identificación.';
      return;
    }

    const data = await res.json();
    contactName.value = data.name;
    props.form.contact_id = data.id;
  } catch {
    contactError.value = 'Error al consultar el contacto.';
  } finally {
    contactResolving.value = false;
  }
});

const n = (v: number | string) => parseFloat(String(v)) || 0;

watch(
  () => props.form.emision,
  (val) => {
    if (val && !props.form.autorized_at) {
      props.form.autorized_at = `${val}T00:00`;
    }
  },
);

watch(
  () => props.form.base12,
  (val) => {
    props.form.iva12 = parseFloat((n(val) * 0.12).toFixed(2));
  },
);

watch(
  () => props.form.base15,
  (val) => {
    props.form.iva15 = parseFloat((n(val) * 0.15).toFixed(2));
  },
);

const computedSubTotal = computed(
  () =>
    n(props.form.no_iva) +
    n(props.form.base0) +
    n(props.form.base12) +
    n(props.form.base15),
);

const computedTotal = computed(
  () =>
    computedSubTotal.value +
    n(props.form.iva12) +
    n(props.form.iva15) +
    n(props.form.ice) -
    n(props.form.discount) -
    n(props.form.aditional_discount),
);

watchEffect(() => {
  props.form.sub_total = parseFloat(computedSubTotal.value.toFixed(2));
  props.form.total = parseFloat(computedTotal.value.toFixed(2));
});

const today = new Date().toISOString().slice(0, 10);
const nowLocal = new Date(new Date() - new Date().getTimezoneOffset() * 60000)
  .toISOString()
  .slice(0, 16);

const emit = defineEmits<{
  submit: [];
}>();
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
        <div class="flex flex-col gap-1.5">
          <Label>Identificación <span class="text-destructive">*</span></Label>
          <div class="relative">
            <Input
              v-model="contactIdentification"
              type="text"
              maxlength="13"
              placeholder="RUC o cédula"
              class="pr-8 font-mono"
              :disabled="contactResolving"
            />
            <span
              v-if="contactResolving"
              class="text-muted-foreground absolute top-1/2 right-2.5 -translate-y-1/2"
            >
              <svg
                class="size-4 animate-spin"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                />
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                />
              </svg>
            </span>
          </div>
          <p v-if="contactError" class="text-muted-foreground text-xs">
            {{ contactError }}
          </p>
          <p v-if="form.errors.contact_id" class="text-destructive text-xs">
            {{ form.errors.contact_id }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <Label>Nombre</Label>
          <Input
            :model-value="contactName"
            type="text"
            readonly
            placeholder="Se completa automáticamente"
            class="bg-muted text-muted-foreground cursor-default"
          />
        </div>

        <div class="flex flex-col gap-1.5">
          <Label
            >Tipo de comprobante <span class="text-destructive">*</span></Label
          >
          <SelectField
            :model-value="form.voucher_type_id"
            :options="voucherTypeOptions"
            placeholder="Seleccionar tipo"
            @update:model-value="(v) => (form.voucher_type_id = Number(v))"
          />
          <p
            v-if="form.errors.voucher_type_id"
            class="text-destructive text-xs"
          >
            {{ form.errors.voucher_type_id }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <Label>Serie <span class="text-destructive">*</span></Label>
          <Input
            v-model="form.serie"
            type="text"
            maxlength="17"
            placeholder="001-001-000000001"
            class="font-mono"
          />
          <p v-if="form.errors.serie" class="text-destructive text-xs">
            {{ form.errors.serie }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <Label
            >Fecha de emisión <span class="text-destructive">*</span></Label
          >
          <Input v-model="form.emision" type="date" min="2015-01-01" :max="today" />
          <p v-if="form.errors.emision" class="text-destructive text-xs">
            {{ form.errors.emision }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5 lg:col-span-2">
          <Label>Autorización <span class="text-destructive">*</span></Label>
          <Input
            v-model="form.autorization"
            type="text"
            maxlength="49"
            class="font-mono"
          />
          <p v-if="form.errors.autorization" class="text-destructive text-xs">
            {{ form.errors.autorization }}
          </p>
        </div>

        <div class="flex flex-col gap-1.5">
          <Label>Fecha de autorización</Label>
          <Input v-model="form.autorized_at" type="datetime-local" min="2015-01-01T00:00" :max="nowLocal" />
          <p v-if="form.errors.autorized_at" class="text-destructive text-xs">
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
        <div class="flex flex-col gap-1.5">
          <Label>No IVA</Label>
          <Input
            v-model="form.no_iva"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Base 0%</Label>
          <Input
            v-model="form.base0"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <!-- <div class="flex flex-col gap-1.5">
          <Label>Base 12%</Label>
          <Input
            v-model="form.base12"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>IVA 12%</Label>
          <Input
            v-model="form.iva12"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div> -->
        <div class="flex flex-col gap-1.5">
          <Label>Base 15%</Label>
          <Input
            v-model="form.base15"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>IVA 15%</Label>
          <Input
            v-model="form.iva15"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Descuento</Label>
          <Input
            v-model="form.discount"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <Label>Subtotal</Label>
          <Input
            :model-value="form.sub_total"
            type="number"
            step="0.01"
            readonly
            class="bg-muted text-muted-foreground cursor-default text-right font-mono"
          />
          <p v-if="form.errors.sub_total" class="text-destructive text-xs">
            {{ form.errors.sub_total }}
          </p>
        </div>
        <!-- <div class="flex flex-col gap-1.5">
          <Label>Desc. adicional</Label>
          <Input
            v-model="form.aditional_discount"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div> -->
        <div class="flex flex-col gap-1.5">
          <Label>ICE</Label>
          <Input
            v-model="form.ice"
            type="number"
            step="0.01"
            min="0"
            class="text-right font-mono"
          />
        </div>
        <div class="bg-muted flex flex-col gap-1 rounded-lg px-4 py-3">
          <Label
            class="text-muted-foreground text-xs font-semibold tracking-wider uppercase"
          >
            Total
          </Label>
          <Input
            :model-value="form.total"
            type="number"
            step="0.01"
            readonly
            class="cursor-default border-0 bg-transparent p-0 text-right font-mono text-base font-semibold shadow-none focus-visible:ring-0"
          />
          <p v-if="form.errors.total" class="text-destructive text-xs">
            {{ form.errors.total }}
          </p>
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
