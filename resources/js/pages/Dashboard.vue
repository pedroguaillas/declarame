<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-foreground">Dashboard</h1>
        </div>

        <div class="overflow-hidden rounded-lg border border-border bg-card">
            <div class="p-6 text-foreground">
                Bienvenido al panel de administración.
            </div>
        </div>
    </AdminLayout>
</template>
