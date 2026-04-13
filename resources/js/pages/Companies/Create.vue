<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  index,
  store,
} from '@/actions/App/Http/Controllers/Tenant/CompanyController';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  ruc: '',
  name: '',
  matrix_address: '',
  special_contribution: false,
  accounting: false,
  retention_agent: false,
  phantom_taxpayer: false,
  no_transactions: false,
  rimpe: '',
  phone: '',
  email: '',
  type_declaration: '',
  pass_sri: '',
});

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
            <Input id="ruc" v-model="form.ruc" type="text" maxlength="13" class="font-mono" />
            <p v-if="form.errors.ruc" class="text-destructive text-sm">{{ form.errors.ruc }}</p>
          </div>

          <div class="grid gap-1.5">
            <Label for="name">Nombre <span class="text-destructive">*</span></Label>
            <Input id="name" v-model="form.name" type="text" maxlength="300" />
            <p v-if="form.errors.name" class="text-destructive text-sm">{{ form.errors.name }}</p>
          </div>

          <div class="grid gap-1.5 sm:col-span-2">
            <Label for="matrix_address">Dirección Matriz <span class="text-destructive">*</span></Label>
            <Input id="matrix_address" v-model="form.matrix_address" type="text" maxlength="300" />
            <p v-if="form.errors.matrix_address" class="text-destructive text-sm">{{ form.errors.matrix_address }}</p>
          </div>

          <div class="grid gap-1.5">
            <Label for="phone">Teléfono</Label>
            <Input id="phone" v-model="form.phone" type="text" maxlength="20" />
            <p v-if="form.errors.phone" class="text-destructive text-sm">{{ form.errors.phone }}</p>
          </div>

          <div class="grid gap-1.5">
            <Label for="email">Email</Label>
            <Input id="email" v-model="form.email" type="email" maxlength="50" />
            <p v-if="form.errors.email" class="text-destructive text-sm">{{ form.errors.email }}</p>
          </div>

          <div class="grid gap-1.5">
            <Label for="rimpe">RIMPE</Label>
            <Input id="rimpe" v-model="form.rimpe" type="text" maxlength="50" />
            <p v-if="form.errors.rimpe" class="text-destructive text-sm">{{ form.errors.rimpe }}</p>
          </div>

          <div class="grid gap-1.5">
            <Label for="type_declaration">Tipo de Declaración</Label>
            <Input id="type_declaration" v-model="form.type_declaration" type="text" />
            <p v-if="form.errors.type_declaration" class="text-destructive text-sm">{{ form.errors.type_declaration }}</p>
          </div>

          <div class="grid gap-1.5">
            <Label for="pass_sri">Clave SRI</Label>
            <Input id="pass_sri" v-model="form.pass_sri" type="password" maxlength="50" />
            <p v-if="form.errors.pass_sri" class="text-destructive text-sm">{{ form.errors.pass_sri }}</p>
          </div>
        </div>

        <div class="border-border mt-6 border-t pt-6">
          <h2 class="text-foreground text-sm font-medium">Características</h2>
          <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <label class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors">
              <span class="text-foreground text-sm">Contribuyente especial</span>
              <Switch v-model:checked="form.special_contribution" />
            </label>
            <label class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors">
              <span class="text-foreground text-sm">Lleva contabilidad</span>
              <Switch v-model:checked="form.accounting" />
            </label>
            <label class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors">
              <span class="text-foreground text-sm">Agente de retención</span>
              <Switch v-model:checked="form.retention_agent" />
            </label>
            <label class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors">
              <span class="text-foreground text-sm">Contribuyente fantasma</span>
              <Switch v-model:checked="form.phantom_taxpayer" />
            </label>
            <label class="border-border hover:bg-accent/50 flex cursor-pointer items-center justify-between rounded-lg border px-4 py-3 transition-colors">
              <span class="text-foreground text-sm">Sin transacciones</span>
              <Switch v-model:checked="form.no_transactions" />
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
