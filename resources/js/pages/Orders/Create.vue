<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ShopForm from '@/pages/Shops/partials/ShopForm.vue';
import { store } from '@/actions/App/Http/Controllers/Tenant/OrderController';
import { Head, useForm } from '@inertiajs/vue3';

interface Option {
    id: number;
    name: string;
}

const props = defineProps<{
    contacts: Option[];
}>();

const form = useForm({
    contact_id: null as number | null,
    voucher_type: '' as number | string,
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
    state: '',
    serie_retention: '',
    date_retention: '',
    state_retention: '',
    autorization_retention: '',
    retention_at: '',
});

const contacts = props.contacts.map((c) => ({ value: c.id, label: c.name }));

function submit() {
    form.post(store.url());
}
</script>

<template>
    <Head title="Nueva Venta" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-foreground">Nueva Venta</h1>
        </div>

        <div class="overflow-hidden rounded-lg border border-border bg-card">
            <ShopForm
                :form="form"
                :contacts="contacts"
                submit-label="Registrar venta"
                @submit="submit"
            />
        </div>
    </AdminLayout>
</template>
