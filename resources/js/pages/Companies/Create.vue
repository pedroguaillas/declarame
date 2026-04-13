<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  index,
  resolve,
  store,
} from '@/actions/App/Http/Controllers/Tenant/CompanyController';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SelectField from '@/components/ui/select/SelectField.vue';
import { Switch } from '@/components/ui/switch';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface ContributorType {
  id: number;
  description: string;
}

const props = defineProps<{
  contributorType: ContributorType[];
}>();

const contributorTypeOptions = computed(() =>
  props.contributorType.map((c) => ({ value: c.id, label: c.description })),
);

const typeDeclarationOptions = [
  { value: 'mensual', label: 'Mensual' },
  { value: 'semestral', label: 'Semestral' },
];

const form = useForm({
  ruc: '',
  name: '',
  matrix_address: '',
  contributor_type_id: null as number | null,
  special_contribution: null as number | null,
  accounting: false,
  retention_agent: null as number | null,
  phantom_taxpayer: false,
  no_transactions: false,
  phone: '',
  email: '',
  type_declaration: '',
  pass_sri: '',
});

const resolving = ref(false);
const resolveError = ref<string | null>(null);

watch(
  () => form.ruc,
  async (ruc) => {
    if (ruc.length !== 13) {
      resolveError.value = null;
      return;
    }

    resolving.value = true;
    resolveError.value = null;

    try {
      const res = await fetch(resolve(ruc).url, {
        headers: { Accept: 'application/json' },
      });

      if (!res.ok) {
        resolveError.value = 'No se encontró información para este RUC.';
        return;
      }

      const data = await res.json();

      if (data?.name) form.name = data.name;
      if (data?.matrix_address) form.matrix_address = data.matrix_address;
      if (data?.phone) form.phone = data.phone;
      if (data?.email) form.email = data.email;
      if (data?.contributor_type_id != null)
        form.contributor_type_id = data.contributor_type_id;
      if (data?.type_declaration) form.type_declaration = data.type_declaration;
      if (data?.special_contribution != null)
        form.special_contribution = data.special_contribution;
      if (data?.accounting !== undefined) form.accounting = data.accounting;
      if (data?.retention_agent != null)
        form.retention_agent = data.retention_agent;
      if (data?.phantom_taxpayer !== undefined)
        form.phantom_taxpayer = data.phantom_taxpayer;
      if (data?.no_transactions !== undefined)
        form.no_transactions = data.no_transactions;
    } catch {
      resolveError.value = 'Error al consultar el RUC.';
    } finally {
      resolving.value = false;
    }
  },
);

function submit() {
  form.post(store.url());
}
</script>

<template>
  <Head title="Nueva Empresa" />

  <AdminLayout>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-foreground text-2xl font-semibold">Nueva Empresa</h1>
      <Link
        :href="index.url()"
        class="text-muted-foreground hover:text-foreground text-sm"
      >
        ← Volver
      </Link>
    </div>

    <div class="border-border bg-card overflow-hidden rounded-lg border">
      <form class="p-6" @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <div class="grid gap-1.5">
            <Label for="ruc">RUC <span class="text-destructive">*</span></Label>
            <div class="relative">
              <Input
                id="ruc"
                v-model="form.ruc"
                type="text"
                maxlength="13"
                class="pr-8 font-mono"
                :disabled="resolving"
              />
              <span
                v-if="resolving"
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
            <p v-if="resolveError" class="text-muted-foreground text-sm">
              {{ resolveError }}
            </p>
            <p v-if="form.errors.ruc" class="text-destructive text-sm">
              {{ form.errors.ruc }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label for="name"
              >Nombre <span class="text-destructive">*</span></Label
            >
            <Input id="name" v-model="form.name" type="text" maxlength="300" />
            <p v-if="form.errors.name" class="text-destructive text-sm">
              {{ form.errors.name }}
            </p>
          </div>

          <div class="grid gap-1.5 sm:col-span-2">
            <Label for="matrix_address"
              >Dirección Matriz <span class="text-destructive">*</span></Label
            >
            <Input
              id="matrix_address"
              v-model="form.matrix_address"
              type="text"
              maxlength="300"
            />
            <p
              v-if="form.errors.matrix_address"
              class="text-destructive text-sm"
            >
              {{ form.errors.matrix_address }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label for="phone">Teléfono</Label>
            <Input id="phone" v-model="form.phone" type="text" maxlength="20" />
            <p v-if="form.errors.phone" class="text-destructive text-sm">
              {{ form.errors.phone }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
              maxlength="50"
            />
            <p v-if="form.errors.email" class="text-destructive text-sm">
              {{ form.errors.email }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label>Tipo de Contribuyente</Label>
            <SelectField
              :model-value="form.contributor_type_id"
              :options="contributorTypeOptions"
              placeholder="Seleccionar tipo"
              @update:model-value="
                (v) => (form.contributor_type_id = Number(v))
              "
            />
            <p
              v-if="form.errors.contributor_type_id"
              class="text-destructive text-sm"
            >
              {{ form.errors.contributor_type_id }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label>Tipo de Declaración</Label>
            <SelectField
              :model-value="form.type_declaration"
              :options="typeDeclarationOptions"
              placeholder="Seleccionar período"
              @update:model-value="(v) => (form.type_declaration = String(v))"
            />
            <p
              v-if="form.errors.type_declaration"
              class="text-destructive text-sm"
            >
              {{ form.errors.type_declaration }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label for="special_contribution">Contribuyente Especial N°</Label>
            <Input
              id="special_contribution"
              :model-value="form.special_contribution ?? undefined"
              type="number"
              min="0"
              placeholder="Número de resolución (opcional)"
              @update:model-value="
                (v) => (form.special_contribution = v !== '' ? Number(v) : null)
              "
            />
            <p
              v-if="form.errors.special_contribution"
              class="text-destructive text-sm"
            >
              {{ form.errors.special_contribution }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label for="retention_agent">Agente de Retención N°</Label>
            <Input
              id="retention_agent"
              :model-value="form.retention_agent ?? undefined"
              type="number"
              min="0"
              placeholder="Número de resolución (opcional)"
              @update:model-value="
                (v) => (form.retention_agent = v !== '' ? Number(v) : null)
              "
            />
            <p
              v-if="form.errors.retention_agent"
              class="text-destructive text-sm"
            >
              {{ form.errors.retention_agent }}
            </p>
          </div>

          <div class="grid gap-1.5">
            <Label for="pass_sri">Clave SRI</Label>
            <Input
              id="pass_sri"
              v-model="form.pass_sri"
              type="password"
              maxlength="50"
            />
            <p v-if="form.errors.pass_sri" class="text-destructive text-sm">
              {{ form.errors.pass_sri }}
            </p>
          </div>
        </div>

        <div class="border-border mt-6 border-t pt-6">
          <h2 class="text-foreground text-sm font-medium">Características</h2>
          <div
            class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
          >
            <label
              class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors"
            >
              <span class="text-foreground text-sm">Lleva contabilidad</span>
              <Switch v-model="form.accounting" />
            </label>
            <label
              class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors"
            >
              <span class="text-foreground text-sm"
                >Contribuyente fantasma</span
              >
              <Switch v-model="form.phantom_taxpayer" />
            </label>
            <label
              class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors"
            >
              <span class="text-foreground text-sm">Sin transacciones</span>
              <Switch v-model="form.no_transactions" />
            </label>
          </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3">
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
            {{ form.processing ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
