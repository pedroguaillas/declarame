<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  create,
  destroy,
  edit,
} from '@/actions/App/Http/Controllers/Tenant/OrderController';
import { Badge } from '@/components/ui/badge';
import { Head, Link, router } from '@inertiajs/vue3';

interface Order {
  id: number;
  serie: string;
  emision: string;
  total: string;
  state: string;
  company: { id: number; name: string };
  contact: { id: number; name: string };
}

defineProps<{
  orders: Order[];
}>();

const stateVariant = (state: string) => {
  const map: Record<string, 'success' | 'warning' | 'outline' | 'destructive'> =
    {
      AUTORIZADO: 'success',
      PENDIENTE: 'warning',
      ANULADO: 'destructive',
    };
  return map[state.toUpperCase()] ?? 'outline';
};

const stateLabel = (state: string) => {
  const map: Record<string, string> = {
    AUTORIZADO: 'Autorizado',
    PENDIENTE: 'Pendiente',
    ANULADO: 'Anulado',
  };
  return map[state.toUpperCase()] ?? state;
};

function deleteOrder(order: Order) {
  if (confirm(`¿Eliminar la venta "${order.serie}"?`)) {
    router.delete(destroy.url(order));
  }
}
</script>

<template>
  <Head title="Ventas" />

  <AdminLayout>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-foreground text-2xl font-semibold">Ventas</h1>
      <Link
        :href="create.url()"
        class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium transition-colors"
      >
        Nueva venta
      </Link>
    </div>

    <div class="border-border bg-card overflow-hidden rounded-lg border">
      <div v-if="orders.length === 0" class="text-muted-foreground p-6">
        No hay ventas registradas.
      </div>

      <table v-else class="divide-border min-w-full divide-y">
        <thead class="bg-muted">
          <tr>
            <th
              class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Serie
            </th>
            <th
              class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Empresa
            </th>
            <th
              class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Cliente
            </th>
            <th
              class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Emisión
            </th>
            <th
              class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase"
            >
              Total
            </th>
            <th
              class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase"
            >
              Estado
            </th>
            <th class="relative px-5 py-3">
              <span class="sr-only">Acciones</span>
            </th>
          </tr>
        </thead>
        <tbody class="divide-border bg-card divide-y">
          <tr
            v-for="order in orders"
            :key="order.id"
            class="hover:bg-muted/40 transition-colors"
          >
            <td
              class="text-foreground px-5 py-3.5 font-mono text-sm whitespace-nowrap"
            >
              {{ order.serie }}
            </td>
            <td
              class="text-foreground max-w-[180px] truncate px-5 py-3.5 text-sm"
            >
              {{ order.company.name }}
            </td>
            <td
              class="text-muted-foreground max-w-[180px] truncate px-5 py-3.5 text-sm"
            >
              {{ order.contact.name }}
            </td>
            <td
              class="text-muted-foreground px-5 py-3.5 text-sm whitespace-nowrap tabular-nums"
            >
              {{ order.emision }}
            </td>
            <td
              class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap"
            >
              ${{ Number(order.total).toFixed(2) }}
            </td>
            <td class="px-5 py-3.5 whitespace-nowrap">
              <Badge :variant="stateVariant(order.state)">{{
                stateLabel(order.state)
              }}</Badge>
            </td>
            <td class="px-5 py-3.5 text-right text-sm whitespace-nowrap">
              <Link
                :href="edit.url(order)"
                class="text-primary hover:text-primary/70 mr-4"
                >Editar</Link
              >
              <button
                type="button"
                class="text-destructive hover:text-destructive/70"
                @click="deleteOrder(order)"
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
