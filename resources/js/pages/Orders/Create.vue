<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Tenant/OrderController';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ShopForm from '@/pages/Shops/partials/ShopForm.vue';
import { VoucherType } from '@/types/voucher-type';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
  voucherTypes: VoucherType[];
}>();

const form = useForm({
  contact_id: null as number | null,
  voucher_type_id: '' as number | string,
  emision: '',
  autorization: '',
  autorized_at: '',
  serie: '',
  sub_total: 0,
  no_iva: 0,
  base0: 0,
  base5: 0,
  base8: 0,
  base12: 0,
  base15: 0,
  iva5: 0,
  iva8: 0,
  iva12: 0,
  iva15: 0,
  aditional_discount: 0,
  discount: 0,
  ice: 0,
  total: 0,
  state: 'CREADO',
  serie_retention: '',
  date_retention: '',
  state_retention: '',
  autorization_retention: '',
  retention_at: '',
});

function submit() {
  form.post(store.url());
}
</script>

<template>
  <Head title="Nueva Venta" />

  <AdminLayout>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-foreground text-2xl font-semibold">Nueva Venta</h1>
    </div>

    <div class="border-border bg-card overflow-hidden rounded-lg border">
      <ShopForm
        :form="form"
        :voucher-types="props.voucherTypes"
        submit-label="Registrar venta"
        @submit="submit"
      />
    </div>
  </AdminLayout>
</template>
