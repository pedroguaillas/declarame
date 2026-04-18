<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  create,
  destroy,
  edit,
  importMethod,
  storeRetention,
  updateAccount,
} from '@/actions/App/Http/Controllers/Tenant/ShopController';
import { Badge } from '@/components/ui/badge';
import Pagination from '@/components/Pagination.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Account {
  id: number;
  code: string;
  name: string;
}

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
  acount_id: number | null;
  serie: string;
  emision: string;
  autorization: string;
  sub_total: string;
  no_iva: string;
  base0: string;
  base5: string;
  base12: string;
  base15: string;
  iva5: string;
  iva12: string;
  iva15: string;
  discount: string;
  ice: string;
  total: string;
  state: string;
  company: { id: number; name: string };
  contact: { id: number; name: string };
  account: Account | null;
  serie_retention: string | null;
  date_retention: string | null;
  state_retention: string | null;
  autorization_retention: string | null;
  retention_items: RetentionItem[];
}

interface Paginator<T> {
  data: T[];
  from: number | null;
  to: number | null;
  total: number;
  prev_page_url: string | null;
  next_page_url: string | null;
  links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
  shops: Paginator<Shop>;
  retentions: RetentionOption[];
  accounts: Account[];
  isRetentionAgent: boolean;
}>();

// ── Import panel ─────────────────────────────────────────────────────────────

const importFileInput = ref<HTMLInputElement | null>(null);
const importForm = useForm<{ file: File | null }>({ file: null });

function triggerImport() {
  importFileInput.value?.click();
}

function handleFileSelected(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (!file) return;
  importForm.file = file;
  importForm.post(importMethod.url(), {
    forceFormData: true,
    onFinish: () => {
      importForm.reset();
      if (importFileInput.value) importFileInput.value.value = '';
    },
  });
}

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

// ── Row selection ────────────────────────────────────────────────────────────

const selectedIds = ref<Set<number>>(new Set());

const selectedCount = computed(() => selectedIds.value.size);
const selectedShops = computed(() => props.shops.data.filter((s) => selectedIds.value.has(s.id)));
const singleSelected = computed(() => (selectedShops.value.length === 1 ? selectedShops.value[0] : null));
const isAllSelected = computed(
  () => props.shops.data.length > 0 && props.shops.data.every((s) => selectedIds.value.has(s.id)),
);
const isIndeterminate = computed(() => selectedCount.value > 0 && !isAllSelected.value);

function toggleSelect(id: number) {
  const next = new Set(selectedIds.value);
  if (next.has(id)) { next.delete(id); } else { next.add(id); }
  selectedIds.value = next;
}

function toggleSelectAll() {
  selectedIds.value = isAllSelected.value
    ? new Set()
    : new Set(props.shops.data.map((s) => s.id));
}

function clearSelection() {
  selectedIds.value = new Set();
}

watch(() => props.shops.prev_page_url, clearSelection);

function deleteSelected() {
  const count = selectedIds.value.size;
  if (!confirm(`¿Eliminar ${count} compra${count > 1 ? 's' : ''}?`)) return;
  const toDelete = [...selectedIds.value];
  clearSelection();
  toDelete.forEach((id) => {
    const shop = props.shops.find((s) => s.id === id);
    if (shop) router.delete(destroy.url(shop), { preserveScroll: true });
  });
}

// ── Account panel ────────────────────────────────────────────────────────────

const accountPanelOpen = ref(false);
const accountPanelEditing = ref(false);
const selectedShopForAccount = ref<Shop | null>(null);
const accountQuery = ref('');
const accountDropdownOpen = ref(false);
const accountForm = useForm<{ acount_id: number | null }>({ acount_id: null });

function openAccountPanel(shop: Shop) {
  selectedShopForAccount.value = shop;
  accountForm.acount_id = shop.acount_id;
  accountPanelEditing.value = !shop.acount_id;
  if (shop.account) {
    accountQuery.value = `${shop.account.code} – ${shop.account.name}`;
  } else {
    accountQuery.value = '';
  }
  accountPanelOpen.value = true;
}

function closeAccountPanel() {
  accountPanelOpen.value = false;
  accountPanelEditing.value = false;
  selectedShopForAccount.value = null;
  accountQuery.value = '';
}

function filteredAccounts(): Account[] {
  const q = accountQuery.value.trim().toLowerCase();
  if (!q) return props.accounts.slice(0, 8);
  return props.accounts
    .filter((a) => a.code.toLowerCase().includes(q) || a.name.toLowerCase().includes(q))
    .slice(0, 8);
}

function selectAccount(account: Account) {
  accountForm.acount_id = account.id;
  accountQuery.value = `${account.code} – ${account.name}`;
  accountDropdownOpen.value = false;
}

function submitAccount() {
  if (!selectedShopForAccount.value) return;
  accountForm.patch(updateAccount.url(selectedShopForAccount.value), {
    onSuccess: () => closeAccountPanel(),
  });
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
      <div class="flex items-center gap-2">
        <input
          ref="importFileInput"
          type="file"
          accept=".txt"
          class="hidden"
          @change="handleFileSelected"
        />
        <button
          type="button"
          :disabled="importForm.processing"
          class="border-border text-foreground hover:bg-accent flex items-center gap-1.5 rounded-md border px-4 py-2 text-sm font-medium transition-colors disabled:opacity-50"
          @click="triggerImport"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
          </svg>
          {{ importForm.processing ? 'Importando…' : 'Importar reporte SRI' }}
        </button>
        <Link
          :href="create.url()"
          class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium transition-colors"
        >
          Nueva compra
        </Link>
      </div>
    </div>

    <div class="border-border bg-card overflow-hidden rounded-lg border">
      <div v-if="shops.data.length === 0" class="text-muted-foreground p-6">
        No hay compras registradas.
      </div>

      <table v-else class="divide-border min-w-full divide-y">
        <thead class="bg-muted">
          <!-- Normal header -->
          <tr v-if="selectedCount === 0">
            <th class="w-10 px-4 py-3">
              <input
                type="checkbox"
                class="border-border text-primary focus:ring-primary/30 h-4 w-4 cursor-pointer rounded"
                :checked="isAllSelected"
                :indeterminate="isIndeterminate"
                @change="toggleSelectAll"
              />
            </th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Emisión</th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Serie</th>
            <th class="text-muted-foreground px-5 py-3 text-left text-xs font-medium tracking-wider uppercase">Proveedor</th>
            <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">Total</th>
            <template v-if="isRetentionAgent">
              <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">Retención</th>
              <th class="text-muted-foreground px-5 py-3 text-right text-xs font-medium tracking-wider uppercase">Pagar</th>
            </template>
            <th class="relative px-5 py-3"><span class="sr-only">Acciones</span></th>
          </tr>

          <!-- Selection toolbar (Gmail-style) -->
          <tr v-else>
            <th :colspan="isRetentionAgent ? 8 : 6" class="px-4 py-2.5">
              <div class="flex items-center gap-3">
                <input
                  type="checkbox"
                  class="border-border text-primary focus:ring-primary/30 h-4 w-4 cursor-pointer rounded"
                  :checked="isAllSelected"
                  :indeterminate="isIndeterminate"
                  @change="toggleSelectAll"
                />
                <span class="text-foreground text-sm font-medium">{{ selectedCount }} seleccionada{{ selectedCount > 1 ? 's' : '' }}</span>
                <div class="bg-border h-4 w-px" />

                <!-- Single-selection actions -->
                <template v-if="singleSelected">
                  <button
                    type="button"
                    class="flex items-center gap-1.5 text-sm font-medium transition-colors"
                    :class="singleSelected!.account ? 'text-blue-600 hover:text-blue-700 dark:text-blue-400' : 'text-muted-foreground hover:text-foreground'"
                    @click="openAccountPanel(singleSelected!)"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    Cuenta
                  </button>
                  <template v-if="isRetentionAgent">
                    <button
                      type="button"
                      class="flex items-center gap-1.5 text-sm font-medium transition-colors"
                      :class="singleSelected!.serie_retention ? 'text-green-600 hover:text-green-700 dark:text-green-400' : 'text-muted-foreground hover:text-foreground'"
                      @click="openRetentionPanel(singleSelected!)"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                      </svg>
                      Retención
                    </button>
                  </template>
                  <Link
                    :href="edit.url(singleSelected!)"
                    class="text-primary hover:text-primary/70 flex items-center gap-1.5 text-sm font-medium transition-colors"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                    </svg>
                    Editar
                  </Link>
                  <div class="bg-border h-4 w-px" />
                </template>

                <!-- Always available: delete -->
                <button
                  type="button"
                  class="text-destructive hover:text-destructive/70 flex items-center gap-1.5 text-sm font-medium transition-colors"
                  @click="deleteSelected"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                  Eliminar
                </button>

                <button type="button" class="text-muted-foreground hover:text-foreground ml-auto text-xs transition-colors" @click="clearSelection">
                  Cancelar
                </button>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="divide-border bg-card divide-y">
          <tr
            v-for="shop in shops.data"
            :key="shop.id"
            class="transition-colors"
            :class="selectedIds.has(shop.id) ? 'bg-primary/5' : 'hover:bg-muted/40'"
          >
            <td class="w-10 px-4 py-3.5">
              <input
                type="checkbox"
                class="border-border text-primary focus:ring-primary/30 h-4 w-4 cursor-pointer rounded"
                :checked="selectedIds.has(shop.id)"
                @change="toggleSelect(shop.id)"
              />
            </td>
            <td class="text-muted-foreground px-5 py-3.5 text-sm whitespace-nowrap tabular-nums">{{ shop.emision }}</td>
            <td class="text-foreground px-5 py-3.5 font-mono text-sm whitespace-nowrap">{{ shop.serie }}</td>
            <td class="text-muted-foreground max-w-[180px] truncate px-5 py-3.5 text-sm">{{ shop.contact.name }}</td>
            <td class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap">
              ${{ Number(shop.total).toFixed(2) }}
            </td>
            <template v-if="isRetentionAgent">
              <td class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap">
                ${{ shop.retention_items?.reduce((s, i) => s + Number(i.value), 0).toFixed(2) ?? '0.00' }}
              </td>
              <td class="text-foreground px-5 py-3.5 text-right font-mono text-sm font-medium whitespace-nowrap">
                ${{ (Number(shop.total) - (shop.retention_items?.reduce((s, i) => s + Number(i.value), 0) ?? 0)).toFixed(2) }}
              </td>
            </template>
            <td class="px-4 py-3.5 text-right whitespace-nowrap">
              <div class="flex items-center justify-end gap-1">
                <!-- Account -->
                <button
                  type="button"
                  class="rounded-md p-1.5 transition-colors"
                  :class="shop.account
                    ? 'text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950'
                    : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                  :title="shop.account ? `Cuenta: ${shop.account.code}` : 'Asignar cuenta contable'"
                  @click="openAccountPanel(shop)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                  </svg>
                </button>
                <!-- Retention -->
                <button
                  v-if="isRetentionAgent"
                  type="button"
                  class="rounded-md p-1.5 transition-colors"
                  :class="shop.serie_retention
                    ? 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950'
                    : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                  :title="shop.serie_retention ? `Retención: ${shop.serie_retention}` : 'Registrar retención'"
                  @click="openRetentionPanel(shop)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                  </svg>
                </button>
                <!-- Edit -->
                <Link
                  :href="edit.url(shop)"
                  class="text-muted-foreground hover:bg-muted hover:text-foreground rounded-md p-1.5 transition-colors"
                  title="Editar"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                  </svg>
                </Link>
                <!-- Delete -->
                <button
                  type="button"
                  class="text-muted-foreground hover:bg-destructive/10 hover:text-destructive rounded-md p-1.5 transition-colors"
                  title="Eliminar"
                  @click="deleteShop(shop)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="shops.data.length > 0" class="border-border border-t">
        <Pagination v-bind="shops" />
      </div>
    </div>

    <!-- Retention slide-over panel -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="retentionPanelOpen" class="fixed inset-0 z-40 bg-black/40" @click="closeRetentionPanel" />
      </Transition>

      <Transition enter-active-class="transition-transform duration-200 ease-out" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition-transform duration-150 ease-in" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
        <div v-if="retentionPanelOpen && selectedShop" class="bg-background border-border fixed inset-y-0 right-0 z-50 flex w-full max-w-4xl border-l shadow-xl">

          <!-- ── Left: invoice info ── -->
          <div class="border-border flex w-72 shrink-0 flex-col border-r">
            <div class="border-border border-b px-5 py-4">
              <p class="text-muted-foreground text-xs font-semibold tracking-widest uppercase">Factura</p>
              <p class="text-foreground mt-0.5 font-mono text-sm font-medium">{{ selectedShop.serie }}</p>
            </div>
            <div class="flex-1 overflow-y-auto p-5 space-y-4">
              <div>
                <p class="text-muted-foreground mb-0.5 text-xs font-medium">Proveedor</p>
                <p class="text-foreground text-sm">{{ selectedShop.contact.name }}</p>
              </div>
              <div>
                <p class="text-muted-foreground mb-0.5 text-xs font-medium">Fecha emisión</p>
                <p class="text-foreground text-sm tabular-nums">{{ selectedShop.emision }}</p>
              </div>
              <div>
                <p class="text-muted-foreground mb-0.5 text-xs font-medium">Clave de acceso</p>
                <p class="text-foreground break-all font-mono text-xs">{{ selectedShop.autorization }}</p>
              </div>

              <!-- IVA breakdown -->
              <div class="border-border rounded-lg border overflow-hidden">
                <table class="min-w-full text-xs">
                  <thead class="bg-muted">
                    <tr>
                      <th class="text-muted-foreground px-3 py-2 text-left font-medium">Concepto</th>
                      <th class="text-muted-foreground px-3 py-2 text-right font-medium">Valor</th>
                    </tr>
                  </thead>
                  <tbody class="divide-border divide-y">
                    <tr v-if="Number(selectedShop.no_iva) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">No IVA</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.no_iva).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.base0) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">Base 0%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.base0).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.base5) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">Base 5%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.base5).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.iva5) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground pl-5">IVA 5%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.iva5).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.base12) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">Base 12%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.base12).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.iva12) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground pl-5">IVA 12%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.iva12).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.base15) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">Base 15%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.base15).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.iva15) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground pl-5">IVA 15%</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.iva15).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.discount) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">Descuento</td>
                      <td class="px-3 py-1.5 text-right font-mono text-destructive">-${{ Number(selectedShop.discount).toFixed(2) }}</td>
                    </tr>
                    <tr v-if="Number(selectedShop.ice) > 0" class="bg-card">
                      <td class="px-3 py-1.5 text-muted-foreground">ICE</td>
                      <td class="px-3 py-1.5 text-right font-mono">${{ Number(selectedShop.ice).toFixed(2) }}</td>
                    </tr>
                    <tr class="bg-muted font-semibold">
                      <td class="px-3 py-2">Total</td>
                      <td class="px-3 py-2 text-right font-mono">${{ Number(selectedShop.total).toFixed(2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ── Right: retention panel ── -->
          <div class="flex flex-1 flex-col">

          <!-- Header -->
          <div class="border-border flex items-start justify-between border-b px-6 py-4">
            <div>
              <h2 class="text-foreground text-base font-semibold">Retención</h2>
              <p class="text-muted-foreground mt-0.5 font-mono text-sm">{{ selectedShop.serie_retention ?? '001-001-000000001' }}</p>
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
                    <th class="text-muted-foreground px-4 py-2.5 text-left text-xs font-medium uppercase">Descripción</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">Base</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">%</th>
                    <th class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">Valor</th>
                  </tr>
                </thead>
                <tbody class="divide-border divide-y">
                  <tr v-for="item in selectedShop.retention_items" :key="item.id" class="bg-card">
                    <td class="px-4 py-2.5">{{ typeLabel[item.retention?.type ?? ''] ?? item.retention?.type }}</td>
                    <td class="px-4 py-2.5 font-mono">{{ item.retention?.code }}</td>
                    <td class="text-muted-foreground px-4 py-2.5 text-sm">{{ item.retention?.description }}</td>
                    <td class="px-4 py-2.5 text-right font-mono">${{ Number(item.base).toFixed(2) }}</td>
                    <td class="px-4 py-2.5 text-right font-mono">{{ item.porcentage }}%</td>
                    <td class="px-4 py-2.5 text-right font-mono font-medium">${{ Number(item.value).toFixed(2) }}</td>
                  </tr>
                </tbody>
                <tfoot class="bg-muted">
                  <tr>
                    <td colspan="5" class="text-muted-foreground px-4 py-2.5 text-right text-xs font-medium uppercase">Total retención</td>
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
                        @blur="() => window.setTimeout(() => { if (itemSearches[idx]) itemSearches[idx].open = false }, 150)"
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
          </div><!-- end right column -->
        </div>
      </Transition>
    </Teleport>

    <!-- Account slide-over panel -->
    <Teleport to="body">
      <Transition enter-active-class="transition-opacity duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="accountPanelOpen" class="fixed inset-0 z-40 bg-black/40" @click="closeAccountPanel" />
      </Transition>

      <Transition enter-active-class="transition-transform duration-200 ease-out" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition-transform duration-150 ease-in" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
        <div v-if="accountPanelOpen && selectedShopForAccount" class="bg-background border-border fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col border-l shadow-xl">

          <!-- Header -->
          <div class="border-border flex items-start justify-between border-b px-6 py-4">
            <div>
              <h2 class="text-foreground text-base font-semibold">Cuenta contable</h2>
              <p class="text-muted-foreground mt-0.5 font-mono text-sm">{{ selectedShopForAccount.serie }}</p>
            </div>
            <button type="button" class="text-muted-foreground hover:text-foreground -mr-1 rounded-md p-1 transition-colors" @click="closeAccountPanel">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- View mode (account already assigned) -->
          <div v-if="selectedShopForAccount.account && accountForm.acount_id === selectedShopForAccount.acount_id && !accountPanelEditing" class="flex flex-1 flex-col p-6">
            <p class="text-muted-foreground mb-1 text-xs font-medium uppercase tracking-wider">Cuenta asignada</p>
            <div class="border-border bg-muted flex items-start gap-3 rounded-lg border px-4 py-3">
              <span class="text-foreground font-mono text-sm font-semibold">{{ selectedShopForAccount.account.code }}</span>
              <span class="text-foreground text-sm">{{ selectedShopForAccount.account.name }}</span>
            </div>
            <button
              type="button"
              class="text-primary hover:text-primary/70 mt-4 self-start text-sm font-medium"
              @click="accountPanelEditing = true"
            >
              Cambiar cuenta
            </button>
          </div>

          <!-- Assign / change form -->
          <form v-else class="flex flex-1 flex-col overflow-hidden" @submit.prevent="submitAccount">
            <div class="flex-1 overflow-y-auto p-6">
              <p class="text-muted-foreground mb-3 text-xs font-medium uppercase tracking-wider">
                Buscar cuenta de costo o gasto
              </p>
              <div class="relative">
                <input
                  v-model="accountQuery"
                  type="text"
                  placeholder="Buscar por código o nombre…"
                  class="border-border bg-background text-foreground placeholder:text-muted-foreground focus:ring-ring/30 h-9 w-full rounded-md border px-3 pr-8 text-sm focus:ring-2 focus:outline-none"
                  @focus="accountDropdownOpen = true"
                  @blur="() => window.setTimeout(() => { accountDropdownOpen = false }, 150)"
                />
                <button
                  v-if="accountForm.acount_id"
                  type="button"
                  class="text-muted-foreground hover:text-foreground absolute top-1/2 right-2.5 -translate-y-1/2"
                  @mousedown.prevent="() => { accountForm.acount_id = null; accountQuery = ''; }"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                  </svg>
                </button>
                <div
                  v-if="accountDropdownOpen && filteredAccounts().length > 0"
                  class="border-border bg-popover absolute left-0 right-0 top-full z-10 mt-1 max-h-60 overflow-y-auto rounded-md border shadow-lg"
                >
                  <button
                    v-for="account in filteredAccounts()"
                    :key="account.id"
                    type="button"
                    class="hover:bg-accent flex w-full items-center gap-3 px-3 py-2 text-left text-sm transition-colors"
                    @mousedown.prevent="selectAccount(account)"
                  >
                    <span class="text-foreground w-28 shrink-0 font-mono text-xs font-semibold">{{ account.code }}</span>
                    <span class="text-muted-foreground flex-1 truncate text-xs">{{ account.name }}</span>
                  </button>
                </div>
              </div>

              <div v-if="accountForm.acount_id" class="border-border bg-muted mt-3 flex items-center gap-3 rounded-lg border px-4 py-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-primary size-4 shrink-0">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span class="text-foreground text-sm">{{ accountQuery }}</span>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-border flex items-center justify-end gap-3 border-t px-6 py-4">
              <button type="button" class="border-border text-foreground hover:bg-accent rounded-md border px-4 py-2 text-sm font-medium transition-colors" @click="closeAccountPanel">
                Cancelar
              </button>
              <button type="submit" :disabled="accountForm.processing || !accountForm.acount_id" class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-4 py-2 text-sm font-medium transition-colors disabled:opacity-50">
                {{ accountForm.processing ? 'Guardando…' : 'Guardar cuenta' }}
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </Teleport>
  </AdminLayout>
</template>
