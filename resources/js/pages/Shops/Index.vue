<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  create,
  destroy,
  edit,
  storeRetention,
} from '@/actions/App/Http/Controllers/Tenant/ShopController';
import { Badge } from '@/components/ui/badge';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface RetentionOption {
  id: number;
  code: string;
  type: string;
  description: string;
  percentage: number;
}

interface RetentionItem {
  id?: number;
  retention_id: number | null;
  retention?: { code: string; description: string; type: string };
  base: number | string;
  porcentage: number | string;
  value: number | string;
}

interface Shop {
  id: number;
  serie: string;
  emision: string;
  total: string;
  sub_total: string;
  state: string;
  company: { id: number; name: string };
  contact: { id: number; name: string };
  serie_retention: string | null;
  date_retention: string | null;
  state_retention: string | null;
  autorization_retention: string | null;
  retention_items: RetentionItem[];
}

const props = defineProps<{
  shops: Shop[];
  retentions: RetentionOption[];
}>();

const stateVariant = (state: string) => {
  const map: Record<string, 'success' | 'warning' | 'outline' | 'destructive'> = {
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

const typeLabel: Record<string, string> = { iva: 'IVA', renta: 'Renta' };

function deleteShop(shop: Shop) {
  if (confirm(`¿Eliminar la compra "${shop.serie}"?`)) {
    router.delete(destroy.url(shop));
  }
}

// ── Retention panel ──────────────────────────────────────────────────────────

const today = new Date().toISOString().slice(0, 10);
const retentionPanelOpen = ref(false);
const selectedShop = ref<Shop | null>(null);

interface ItemSearch { query: string; open: boolean }
const itemSearches = ref<ItemSearch[]>([]);

function emptyItem(subTotal: string | number = 0): RetentionItem {
  return { retention_id: null, base: subTotal, porcentage: '', value: '' };
}

function emptySearch(): ItemSearch {
  return { query: '', open: false };
}

function openRetentionPanel(shop: Shop) {
  selectedShop.value = shop;

  if (!shop.serie_retention) {
    retentionForm.reset();
    retentionForm.items = [emptyItem(shop.sub_total)];
    itemSearches.value = [emptySearch()];
  }

  retentionPanelOpen.value = true;
}

function closeRetentionPanel() {
  retentionPanelOpen.value = false;
  selectedShop.value = null;
}

const retentionForm = useForm<{
  serie_retention: string;
  date_retention: string;
  autorization_retention: string;
  items: Array<{ retention_id: number | null; base: number | string; porcentage: number | string; value: number | string }>;
}>({
  serie_retention: '',
  date_retention: '',
  autorization_retention: '',
  items: [emptyItem()],
});

function addItem() {
  retentionForm.items.push(emptyItem(selectedShop.value?.sub_total ?? 0));
  itemSearches.value.push(emptySearch());
}

function removeItem(index: number) {
  retentionForm.items.splice(index, 1);
  itemSearches.value.splice(index, 1);
}

function filteredRetentions(idx: number): RetentionOption[] {
  const q = itemSearches.value[idx]?.query.trim().toLowerCase();
  if (!q) return props.retentions.slice(0, 8);
  return props.retentions
    .filter((r) => r.code.toLowerCase().includes(q) || r.description.toLowerCase().includes(q))
    .slice(0, 8);
}

function selectRetention(idx: number, retention: RetentionOption) {
  const item = retentionForm.items[idx];
  item.retention_id = retention.id;
  item.porcentage = retention.percentage;
  recalcValue(idx);
  itemSearches.value[idx].query = `${retention.code} – ${retention.description}`;
  itemSearches.value[idx].open = false;
}

function recalcValue(idx: number) {
  const item = retentionForm.items[idx];
  const base = parseFloat(String(item.base)) || 0;
  const pct = parseFloat(String(item.porcentage)) || 0;
  item.value = parseFloat((base * pct / 100).toFixed(2));
}

function submitRetention() {
  if (!selectedShop.value) return;
  retentionForm.post(storeRetention.url(selectedShop.value), {
    onSuccess: () => closeRetentionPanel(),
  });
}
</script>

<template>
  <Head title="Compras" />

  <AdminLayout>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-foreground text-2xl font-semibold">Compras</h1>
      <Link
        :href="create.url()"
        class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium transition-colors"
      >
        Nueva compra
      </Link>
    </div>

    <div class="border-border bg-card overflow-hidden rounded-lg border">
      <div v-if="shops.length === 0" class="text-muted-foreground p-6">
        No hay compras registradas.
      </div>

      <table v-else class="divide-border min-w-full divide-y">
        <thead class="bg-muted">
          <tr>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Emisión</th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Serie</th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Proveedor</th>
            <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">Total</th>
            <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">Retención</th>
            <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">Pagar</th>
            <th class="relative px-5 py-3"><span class="sr-only">Acciones</span></th>
          </tr>
        </thead>
        <tbody class="divide-border bg-card divide-y">
          <tr v-for="shop in shops" :key="shop.id" class="group hover:bg-muted/40 transition-colors">
            <td class="text-muted-foreground px-5 py-3.5 text-sm whitespace-nowrap tabular-nums">{{ shop.emision }}</td>
            <td class="text-foreground px-5 py-3.5 font-mono text-sm whitespace-nowrap">{{ shop.serie }}</td>
            <td class="text-muted-foreground max-w-[180px] truncate px-5 py-3.5 text-sm">{{ shop.contact.name }}</td>
            <td class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap">
              ${{ Number(shop.total).toFixed(2) }}
            </td>
            <td class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap">
              ${{ shop.retention_items?.reduce((s, i) => s + Number(i.value), 0).toFixed(2) ?? '0.00' }}
            </td>
            <td class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap">
              ${{ (Number(shop.total) - (shop.retention_items?.reduce((s, i) => s + Number(i.value), 0) ?? 0)).toFixed(2) }}
            </td>
            <td class="px-5 py-3.5 text-right text-sm whitespace-nowrap">
              <div class="flex items-center justify-end gap-3 opacity-0 transition-opacity group-hover:opacity-100">
                <button
                  type="button"
                  class="flex items-center gap-1 text-sm font-medium transition-colors"
                  :class="shop.serie_retention
                    ? 'text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300'
                    : 'text-muted-foreground hover:text-foreground'"
                  :title="shop.serie_retention ? 'Ver retención' : 'Registrar retención'"
                  @click="openRetentionPanel(shop)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                  </svg>
                  Retención
                </button>
                <div class="bg-border h-3.5 w-px" />
                <Link :href="edit.url(shop)" class="text-primary hover:text-primary/70 font-medium">Editar</Link>
                <button type="button" class="text-destructive hover:text-destructive/70 font-medium" @click="deleteShop(shop)">Eliminar</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Retention slide-over panel -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="retentionPanelOpen" class="fixed inset-0 z-40 bg-black/40" @click="closeRetentionPanel" />
      </Transition>

      <Transition enter-active-class="transition-transform duration-200 ease-out" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition-transform duration-150 ease-in" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
        <div v-if="retentionPanelOpen && selectedShop" class="bg-background border-border fixed inset-y-0 right-0 z-50 flex w-full max-w-2xl flex-col border-l shadow-xl">

          <!-- Header -->
          <div class="border-border flex items-start justify-between border-b px-6 py-4">
            <div>
              <h2 class="text-foreground text-base font-semibold">Retención</h2>
              <p class="text-muted-foreground mt-0.5 font-mono text-sm">{{ selectedShop.serie }}</p>
            </div>
            <button type="button" class="text-muted-foreground hover:text-foreground -mr-1 rounded-md p-1 transition-colors" @click="closeRetentionPanel">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- ── Registered view ── -->
          <div v-if="selectedShop.serie_retention" class="flex-1 overflow-y-auto p-6">
            <div class="mb-6 grid grid-cols-2 gap-4">
              <div>
                <p class="text-muted-foreground mb-1 text-xs font-medium uppercase tracking-wider">Serie</p>
                <p class="text-foreground font-mono text-sm font-medium">{{ selectedShop.serie_retention }}</p>
              </div>
              <div>
                <p class="text-muted-foreground mb-1 text-xs font-medium uppercase tracking-wider">Fecha</p>
                <p class="text-foreground text-sm">{{ selectedShop.date_retention }}</p>
              </div>
              <div class="col-span-2">
                <p class="text-muted-foreground mb-1 text-xs font-medium uppercase tracking-wider">Autorización</p>
                <p class="text-foreground break-all font-mono text-sm">{{ selectedShop.autorization_retention }}</p>
              </div>
              <div>
                <p class="text-muted-foreground mb-1 text-xs font-medium uppercase tracking-wider">Estado</p>
                <Badge :variant="stateVariant(selectedShop.state_retention ?? '')">
                  {{ stateLabel(selectedShop.state_retention ?? '') }}
                </Badge>
              </div>
            </div>

            <p class="text-muted-foreground mb-3 text-xs font-medium uppercase tracking-wider">Detalle</p>
            <div class="border-border overflow-hidden rounded-lg border">
              <table class="divide-border min-w-full divide-y text-sm">
                <thead class="bg-muted">
                  <tr>
                    <th class="text-muted-foreground px-4 py-2.5 text-left text-xs font-medium uppercase">Tipo</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-left text-xs font-medium uppercase">Código</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">Base</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">%</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">Valor</th>
                  </tr>
                </thead>
                <tbody class="divide-border divide-y">
                  <tr v-for="item in selectedShop.retention_items" :key="item.id" class="bg-card">
                    <td class="px-4 py-2.5">{{ typeLabel[item.retention?.type ?? ''] ?? item.retention?.type }}</td>
                    <td class="px-4 py-2.5 font-mono">{{ item.retention?.code }}</td>
                    <td class="px-4 py-2.5 text-right font-mono">${{ Number(item.base).toFixed(2) }}</td>
                    <td class="px-4 py-2.5 text-right font-mono">{{ item.porcentage }}%</td>
                    <td class="px-4 py-2.5 text-right font-mono font-medium">${{ Number(item.value).toFixed(2) }}</td>
                  </tr>
                </tbody>
                <tfoot class="bg-muted">
                  <tr>
                    <td colspan="4" class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">Total retención</td>
                    <td class="px-4 py-2.5 text-right font-mono font-semibold">
                      ${{ selectedShop.retention_items.reduce((s, i) => s + Number(i.value), 0).toFixed(2) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- ── Registration form ── -->
          <form v-else class="flex flex-1 flex-col overflow-hidden" @submit.prevent="submitRetention">
            <div class="flex-1 space-y-5 overflow-y-auto p-6">

              <!-- Header fields -->
              <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                  <label class="text-foreground text-sm font-medium">Serie <span class="text-destructive">*</span></label>
                  <input
                    v-model="retentionForm.serie_retention"
                    type="text"
                    maxlength="17"
                    placeholder="001-001-000000001"
                    class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:ring-ring/30 h-9 rounded-md border px-3 font-mono text-sm focus:ring-2 focus:outline-none"
                  />
                  <p v-if="retentionForm.errors.serie_retention" class="text-destructive text-xs">{{ retentionForm.errors.serie_retention }}</p>
                </div>

                <div class="flex flex-col gap-1.5">
                  <label class="text-foreground text-sm font-medium">Fecha <span class="text-destructive">*</span></label>
                  <input
                    v-model="retentionForm.date_retention"
                    type="date"
                    min="2015-01-01"
                    :max="today"
                    class="border-border bg-background text-foreground focus:ring-ring/30 h-9 rounded-md border px-3 text-sm focus:ring-2 focus:outline-none"
                  />
                  <p v-if="retentionForm.errors.date_retention" class="text-destructive text-xs">{{ retentionForm.errors.date_retention }}</p>
                </div>

                <div class="col-span-2 flex flex-col gap-1.5">
                  <label class="text-foreground text-sm font-medium">Autorización <span class="text-destructive">*</span></label>
                  <input
                    v-model="retentionForm.autorization_retention"
                    type="text"
                    maxlength="49"
                    class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:ring-ring/30 h-9 rounded-md border px-3 font-mono text-sm focus:ring-2 focus:outline-none"
                  />
                  <p v-if="retentionForm.errors.autorization_retention" class="text-destructive text-xs">{{ retentionForm.errors.autorization_retention }}</p>
                </div>
              </div>

              <!-- Items -->
              <div>
                <div class="mb-3 flex items-center justify-between">
                  <p class="text-muted-foreground text-xs font-medium uppercase tracking-wider">Detalle de retenciones</p>
                  <button type="button" class="text-primary hover:text-primary/70 text-xs font-medium" @click="addItem">
                    + Agregar fila
                  </button>
                </div>

                <div class="space-y-2">
                  <div
                    v-for="(item, idx) in retentionForm.items"
                    :key="idx"
                    class="border-border bg-card rounded-lg border p-3"
                  >
                    <!-- Autocomplete search -->
                    <div class="relative mb-2">
                      <label class="text-muted-foreground mb-1 block text-xs font-medium">Código / Concepto</label>
                      <input
                        v-model="itemSearches[idx].query"
                        type="text"
                        placeholder="Buscar por código o concepto…"
                        class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:ring-ring/30 h-9 w-full rounded-md border px-3 text-sm focus:ring-2 focus:outline-none"
                        @focus="itemSearches[idx].open = true"
                        @blur="() => setTimeout(() => { if (itemSearches[idx]) itemSearches[idx].open = false }, 150)"
                      />
                      <!-- Dropdown -->
                      <div
                        v-if="itemSearches[idx].open && filteredRetentions(idx).length > 0"
                        class="border-border bg-popover absolute left-0 right-0 top-full z-10 mt-1 max-h-52 overflow-y-auto rounded-md border shadow-lg"
                      >
                        <button
                          v-for="ret in filteredRetentions(idx)"
                          :key="ret.id"
                          type="button"
                          class="hover:bg-accent flex w-full items-center gap-3 px-3 py-2 text-left text-sm transition-colors"
                          @mousedown.prevent="selectRetention(idx, ret)"
                        >
                          <span class="text-foreground w-14 shrink-0 font-mono font-medium">{{ ret.code }}</span>
                          <span class="text-muted-foreground flex-1 truncate text-xs">{{ ret.description }}</span>
                          <span class="text-foreground shrink-0 font-mono text-xs font-medium">{{ ret.percentage }}%</span>
                        </button>
                      </div>
                    </div>

                    <!-- Numeric fields -->
                    <div class="grid grid-cols-3 gap-2">
                      <div class="flex flex-col gap-1">
                        <label class="text-muted-foreground text-xs font-medium">Base</label>
                        <input
                          v-model="item.base"
                          type="number"
                          step="0.01"
                          min="0"
                          class="border-border bg-background text-foreground focus:ring-ring/30 h-8 w-full rounded border px-2 text-right font-mono text-xs focus:ring-1 focus:outline-none"
                          @input="recalcValue(idx)"
                        />
                      </div>
                      <div class="flex flex-col gap-1">
                        <label class="text-muted-foreground text-xs font-medium">% Retención</label>
                        <input
                          v-model="item.porcentage"
                          type="number"
                          step="0.01"
                          min="0"
                          class="border-border bg-background text-foreground focus:ring-ring/30 h-8 w-full rounded border px-2 text-right font-mono text-xs focus:ring-1 focus:outline-none"
                          @input="recalcValue(idx)"
                        />
                      </div>
                      <div class="flex flex-col gap-1">
                        <label class="text-muted-foreground text-xs font-medium">Valor retenido</label>
                        <input
                          :value="Number(item.value).toFixed(2)"
                          type="text"
                          readonly
                          class="border-border bg-muted text-foreground focus:ring-ring/30 h-8 w-full cursor-default rounded border px-2 text-right font-mono text-xs font-semibold"
                        />
                      </div>
                    </div>

                    <!-- Remove -->
                    <div v-if="retentionForm.items.length > 1" class="mt-2 flex justify-end">
                      <button type="button" class="text-muted-foreground hover:text-destructive flex items-center gap-1 text-xs transition-colors" @click="removeItem(idx)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3.5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        Quitar fila
                      </button>
                    </div>
                  </div>
                </div>

                <p v-if="retentionForm.errors.items" class="text-destructive mt-1 text-xs">{{ retentionForm.errors.items }}</p>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-border flex items-center justify-end gap-3 border-t px-6 py-4">
              <button type="button" class="border-border text-foreground hover:bg-accent rounded-md border px-4 py-2 text-sm font-medium transition-colors" @click="closeRetentionPanel">
                Cancelar
              </button>
              <button type="submit" :disabled="retentionForm.processing" class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium transition-colors disabled:opacity-50">
                {{ retentionForm.processing ? 'Guardando…' : 'Guardar retención' }}
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>
