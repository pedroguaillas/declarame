<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { create, edit, destroy } from '@/actions/App/Http/Controllers/Tenant/CompanyController';
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
            <h1 class="text-2xl font-semibold text-foreground">Empresas</h1>
            <Link
                :href="create.url()"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
            >
                Nueva empresa
            </Link>
        </div>

        <div class="overflow-hidden rounded-lg border border-border bg-card">
            <div v-if="companies.length === 0" class="p-6 text-muted-foreground">
                No hay empresas registradas.
            </div>

            <table v-else class="min-w-full divide-y divide-border">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            RUC
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            Teléfono
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            Contabilidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            Ag. Retención
                        </th>
                        <th class="relative px-6 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-card">
                    <tr v-for="company in companies" :key="company.id">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-mono text-foreground">
                            {{ company.ruc }}
                        </td>
                        <td class="px-6 py-4 text-sm text-foreground">
                            {{ company.name }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                            {{ company.phone ?? '—' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-muted-foreground">
                            {{ company.email ?? '—' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <span
                                :class="company.accounting
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-muted text-muted-foreground'"
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                            >
                                {{ company.accounting ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <span
                                :class="company.retention_agent
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-muted text-muted-foreground'"
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                            >
                                {{ company.retention_agent ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                            <Link
                                :href="edit.url(company)"
                                class="mr-4 text-primary hover:text-primary/70"
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
