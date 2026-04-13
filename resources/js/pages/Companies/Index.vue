<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  create,
  destroy,
  edit,
} from '@/actions/App/Http/Controllers/Tenant/CompanyController';
import { Head, Link, router } from '@inertiajs/vue3';

interface Company {
  id: number;
  ruc: string;
  name: string;
  matrix_address: string;
  phone: string | null;
  email: string | null;
  accounting: boolean;
  retention_agent: boolean;
}

defineProps<{
  companies: Company[];
}>();

function deleteCompany(company: Company) {
  if (confirm(`¿Eliminar la empresa "${company.name}"?`)) {
    router.delete(destroy.url(company));
  }
}
</script>

<template>
  <Head title="Empresas" />

  <AdminLayout>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-foreground text-2xl font-semibold">Empresas</h1>
      <Link
        :href="create.url()"
        class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium"
      >
        Nueva empresa
      </Link>
    </div>

    <div class="border-border bg-card overflow-hidden rounded-lg border">
      <div v-if="companies.length === 0" class="text-muted-foreground p-6">
        No hay empresas registradas.
      </div>

      <table v-else class="divide-border min-w-full divide-y">
        <thead class="bg-muted">
          <tr>
            <th
              class="text-muted-foreground px-6 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              RUC
            </th>
            <th
              class="text-muted-foreground px-6 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Nombre
            </th>
            <th
              class="text-muted-foreground px-6 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Contabilidad
            </th>
            <th
              class="text-muted-foreground px-6 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Ag. Retención
            </th>
            <th
              class="text-muted-foreground px-6 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Teléfono
            </th>
            <th
              class="text-muted-foreground px-6 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Email
            </th>
            <th class="relative px-6 py-3">
              <span class="sr-only">Acciones</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-border bg-card divide-y">
          <tr v-for="company in companies" :key="company.id">
            <td
              class="text-foreground px-6 py-4 font-mono text-sm whitespace-nowrap"
            >
              {{ company.ruc }}
            </td>
            <td class="text-foreground px-6 py-4 text-sm">
              {{ company.name }}
            </td>
            <td class="px-6 py-4 text-sm whitespace-nowrap">
              <span
                :class="
                  company.accounting
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                    : 'bg-muted text-muted-foreground'
                "
                class="rounded-full px-2 py-0.5 text-xs font-medium"
              >
                {{ company.accounting ? 'Sí' : 'No' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm whitespace-nowrap">
              <span
                :class="
                  company.retention_agent
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                    : 'bg-muted text-muted-foreground'
                "
                class="rounded-full px-2 py-0.5 text-xs font-medium"
              >
                {{ company.retention_agent ? 'Sí' : 'No' }}
              </span>
            </td>
            <td
              class="text-muted-foreground px-6 py-4 text-sm whitespace-nowrap"
            >
              {{ company.phone ?? '—' }}
            </td>
            <td
              class="text-muted-foreground px-6 py-4 text-sm whitespace-nowrap"
            >
              {{ company.email ?? '—' }}
            </td>
            <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
              <Link
                :href="edit.url(company)"
                class="text-primary hover:text-primary/70 mr-4"
              >
                Editar
              </Link>
              <button
                type="button"
                class="text-destructive hover:text-destructive/70"
                @click="deleteCompany(company)"
              >
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
