<script setup>

import { Head } from '@inertiajs/vue3';
import Header from './Admin/Header.vue'
import Sidebar from './Admin/Sidebar.vue'
import { ref, watch } from 'vue';

defineProps({
    title: String,
})

// Refs
const divRef = ref(null);
const menu = ref(false);

const toggle = () => {
    menu.value = !menu.value;
};

watch(divRef, () => {
    if (divRef.value.getBoundingClientRect().width > 640) {
        toggle();
    }
    // divRef si altura del main mas altura del Header > h-screen ? h-full : h-screen
})

</script>

<template>

    <Head :title="title" />

    <div ref="divRef" class="w-screen h-screen flex">

        <!-- Sidebar -->
        <Sidebar :menu="menu" @toggle="toggle" />

        <!-- Main -->
        <div class="w-full h-full">
            <Header :menu="menu" @toggle="toggle" />
            <div class="h-[calc(100vh-50px)] bg-slate-200 dark:bg-slate-700 p-4 overflow-y-auto">
                <slot />

            </div>
        </div>
    </div>
</template>