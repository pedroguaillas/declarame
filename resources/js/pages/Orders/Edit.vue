<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ShopForm from '@/pages/Shops/partials/ShopForm.vue';
import { update } from '@/actions/App/Http/Controllers/Tenant/OrderController';
import { Head, useForm } from '@inertiajs/vue3';

interface Option {
    id: number;
    name: string;
}

interface Order {
    id: number;
    contact_id: number;
    voucher_type: number;
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
    order: Order;
    contacts: Option[];
}>();

const form = useForm({
    contact_id: props.order.contact_id,
    voucher_type: props.order.voucher_type,
    emision: props.order.emision,
    autorization: props.order.autorization,
    autorized_at: props.order.autorized_at ?? '',
    serie: props.order.serie,
    sub_total: props.order.sub_total,
    no_iva: props.order.no_iva,
    base0: props.order.base0,
    base5: props.order.base5,
    base8: props.order.base8,
    base12: props.order.base12,
    base15: props.order.base15,
    iva5: props.order.iva5,
    iva8: props.order.iva8,
    iva12: props.order.iva12,
    iva15: props.order.iva15,
    aditional_discount: props.order.aditional_discount,
    discount: props.order.discount,
    ice: props.order.ice,
    total: props.order.total,
    state: props.order.state,
    serie_retention: props.order.serie_retention ?? '',
    date_retention: props.order.date_retention ?? '',
    state_retention: props.order.state_retention ?? '',
    autorization_retention: props.order.autorization_retention ?? '',
    retention_at: props.order.retention_at ?? '',
});

const contacts = props.contacts.map((c) => ({ value: c.id, label: c.name }));

function submit() {
    form.put(update.url(props.order));
}
</script>

<template>
    <Head title="Editar Venta" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-foreground">Editar Venta</h1>
        </div>

        <div class="overflow-hidden rounded-lg border border-border bg-card">
            <ShopForm
                :form="form"
                :contacts="contacts"
                submit-label="Actualizar venta"
                @submit="submit"
            />
        </div>
    </AdminLayout>
</template>
