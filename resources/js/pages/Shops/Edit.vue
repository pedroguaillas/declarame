<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ShopForm from './partials/ShopForm.vue';
import { update } from '@/actions/App/Http/Controllers/Tenant/ShopController';
import { VoucherType } from '@/types/voucher-type';
import { Head, useForm } from '@inertiajs/vue3';

interface Shop {
    id: number;
    contact_id: number;
    voucher_type_id: number;
    emision: string;
    autorization: string;
    autorized_at: string | null;
    serie: string;
    sub_total: string;
    no_iva: string;
    base0: string;
    base5: string;
    base8: string;
    base12: string;
    base15: string;
    iva5: string;
    iva8: string;
    iva12: string;
    iva15: string;
    aditional_discount: string;
    discount: string;
    ice: string;
    total: string;
    state: string;
    serie_retention: string | null;
    date_retention: string | null;
    state_retention: string | null;
    autorization_retention: string | null;
    retention_at: string | null;
}

const props = defineProps<{
    shop: Shop;
    voucherTypes: VoucherType[];
}>();

const form = useForm({
    contact_id: props.shop.contact_id,
    voucher_type_id: props.shop.voucher_type_id,
    emision: props.shop.emision,
    autorization: props.shop.autorization,
    autorized_at: props.shop.autorized_at ?? '',
    serie: props.shop.serie,
    sub_total: props.shop.sub_total,
    no_iva: props.shop.no_iva,
    base0: props.shop.base0,
    base5: props.shop.base5,
    base8: props.shop.base8,
    base12: props.shop.base12,
    base15: props.shop.base15,
    iva5: props.shop.iva5,
    iva8: props.shop.iva8,
    iva12: props.shop.iva12,
    iva15: props.shop.iva15,
    aditional_discount: props.shop.aditional_discount,
    discount: props.shop.discount,
    ice: props.shop.ice,
    total: props.shop.total,
    state: props.shop.state,
    serie_retention: props.shop.serie_retention ?? '',
    date_retention: props.shop.date_retention ?? '',
    state_retention: props.shop.state_retention ?? '',
    autorization_retention: props.shop.autorization_retention ?? '',
    retention_at: props.shop.retention_at ?? '',
});

function submit() {
    form.put(update.url(props.shop));
}
</script>

<template>
    <Head title="Editar Compra" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-foreground">Editar Compra</h1>
        </div>

        <div class="overflow-hidden rounded-lg border border-border bg-card">
            <ShopForm
                :form="form"
                :voucher-types="props.voucherTypes"
                submit-label="Actualizar compra"
                @submit="submit"
            />
        </div>
    </AdminLayout>
</template>
