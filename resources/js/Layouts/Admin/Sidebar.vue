<script setup>

// Imports
import { Link } from '@inertiajs/vue3';

// Emits
defineEmits(['toggle']);

// Props
defineProps({
    menu: Boolean
});

</script>

<template>
    <aside class="w-[16em] h-[100vh] p-4 flex flex-col z-40 fixed sm:relative bg-slate-800 ease-in-out duration-300"
        v-show="menu">
        <header class="p-2 [&>a]:w-full">
            <Link class="rounded hover:bg-slate-500 p-2 text-xl text-white" :href="route('dashboard')">
            <strong>ATS</strong>
            Declarame</Link>
            <button @click="$emit('toggle')"
                class="w-6 h-6 text-slate-200 text-3xl sm:hidden inline-block right-2 top-2 absolute rounded-full">
                <i class="fa fa-close"></i>
            </button>
        </header>
        <nav class="p-2">
            <ul class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:p-2 [&>li>a>i]:stroke-white">
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('shops')"
                        :class="route().current('select.category', 'shops') ? 'bg-slate-500' : null">
                    <i class="fa fa-medal"></i> Compras
                    </Link>
                </li>
                <!-- <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('select.category', 'partidos')"
                        :class="route().current('select.category', 'partidos') || route().current('games.index') ? 'bg-slate-500' : null">
                    <i class="fa fa-futbol-ball"></i> Partidos
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('calendar')"
                        :class="route().current('calendar') ? 'bg-slate-500' : null">
                    <i class="fa fa-calendar"></i> Calendario
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('select.category', 'tabla-de-posiciones')"
                        :class="route().current('select.category', 'tabla-de-posiciones') || route().current('tabla-de-posiciones.index') ? 'bg-slate-500' : null">
                    <i class="fa fa-align-center"></i> Tabla de posiciones
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('select.category', 'tabla-de-goleadores')"
                        :class="route().current('select.category', 'tabla-de-goleadores') || route().current('tabla-de-goleadores.index') ? 'bg-slate-500' : null">
                    <i class="fa fa-align-right"></i> Goleadores
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('santions.index')"
                        :class="route().current('santions.index') ? 'bg-slate-500' : null">
                    <i class="fa fa-bell"></i> Sanciones
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('diary')"
                        :class="route().current('diary') ? 'bg-slate-500' : null">
                    <i class="fa fa-money-bill"></i> Económico
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('categories.index')"
                        :class="route().current('categories.index') || route().current('groups.index') ? 'bg-slate-500' : null">
                    <i class="fa fa-cog"></i> Categorias
                    </Link>
                </li> -->
            </ul>
        </nav>
    </aside>
</template>