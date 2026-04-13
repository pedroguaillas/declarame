<script setup lang="ts">
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import { store as storeCompanyScope } from '@/actions/App/Http/Controllers/Tenant/CompanyScopeController';
import { useColorMode } from '@/composables/useColorMode';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const companies = computed(() => (page.props as any).companies ?? []);
const currentCompany = computed(() => (page.props as any).currentCompany ?? null);
const { isDark, toggle } = useColorMode();

// Reactive dark state so the icon updates on toggle
const dark = ref(false);
onMounted(() => {
  dark.value = isDark();
});

function handleToggle() {
  toggle();
  dark.value = isDark();
}

function handleCompanyChange(event: Event) {
  const value = (event.target as HTMLSelectElement).value;
  if (!value) return;
  router.post(storeCompanyScope.url(), { company_id: Number(value) }, {
    preserveScroll: true,
    preserveState: true,
  });
}

const navigation = [
  {
    label: 'Dashboard',
    routeName: 'tenant.dashboard',
    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[18px]"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>`,
  },
  {
    label: 'Empresas',
    routeName: 'tenant.companies.index',
    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[18px]"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>`,
  },
  {
    label: 'Compras',
    routeName: 'tenant.shops.index',
    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[18px]"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>`,
  },
  {
    label: 'Ventas',
    routeName: 'tenant.orders.index',
    icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[18px]"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" /></svg>`,
  },
];

const isActive = (routeName: string) => route().current(routeName);
</script>

<template>
  <div class="bg-background text-foreground min-h-screen">
    <!-- Header -->
    <header
      class="border-border bg-background fixed inset-x-0 top-0 z-20 flex h-14 items-center gap-3 border-b px-4"
    >
      <!-- Brand -->
      <Link
        :href="route('tenant.dashboard')"
        class="flex shrink-0 items-center gap-2.5"
      >
        <ApplicationLogo class="text-foreground h-6 w-auto fill-current" />
        <span class="text-foreground text-sm font-semibold tracking-tight">
          Declárame
        </span>
      </Link>

      <div class="bg-border h-4 w-px" />

      <!-- Company scope selector -->
      <div class="relative flex items-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="text-muted-foreground pointer-events-none absolute left-2.5 size-[15px]"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"
          />
        </svg>
        <select
          :value="currentCompany?.id ?? ''"
          class="border-border bg-background text-foreground focus:ring-ring/30 h-8 w-56 appearance-none rounded-md border pl-8 pr-7 text-sm focus:ring-2 focus:outline-none"
          :class="currentCompany ? 'font-medium' : 'text-muted-foreground'"
          @change="handleCompanyChange"
        >
          <option value="" disabled>Seleccionar empresa…</option>
          <option
            v-for="company in companies"
            :key="company.id"
            :value="company.id"
          >
            {{ company.name }}
          </option>
        </select>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="text-muted-foreground pointer-events-none absolute right-2 size-[13px]"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m19.5 8.25-7.5 7.5-7.5-7.5"
          />
        </svg>
      </div>

      <div class="ml-auto flex items-center gap-1">
        <!-- Theme toggle -->
        <button
          type="button"
          class="text-muted-foreground hover:bg-accent hover:text-foreground focus-visible:ring-ring flex h-8 w-8 items-center justify-center rounded-md transition-colors focus-visible:ring-2 focus-visible:outline-none"
          :aria-label="dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          @click="handleToggle"
        >
          <!-- Sun — visible in dark mode -->
          <svg
            v-if="dark"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-[18px]"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
            />
          </svg>
          <!-- Moon — visible in light mode -->
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-[18px]"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
            />
          </svg>
        </button>

        <div class="bg-border mx-2 h-4 w-px" />

        <!-- User name -->
        <span class="text-muted-foreground text-sm">{{ user?.name }}</span>

        <!-- Logout -->
        <Link
          :href="route('tenant.logout')"
          method="post"
          as="button"
          class="text-muted-foreground hover:bg-accent hover:text-foreground ml-1 rounded-md px-2.5 py-1.5 text-sm transition-colors"
        >
          Salir
        </Link>
      </div>
    </header>

    <div class="flex pt-14">
      <!-- Sidebar -->
      <aside
        class="border-border bg-sidebar fixed inset-y-0 top-14 left-0 w-56 border-r"
      >
        <nav class="flex flex-col gap-0.5 p-2 pt-3">
          <template v-for="item in navigation" :key="item.routeName">
            <Link
              :href="route(item.routeName)"
              class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-medium transition-colors"
              :class="
                isActive(item.routeName)
                  ? 'bg-sidebar-accent text-sidebar-accent-foreground'
                  : 'text-sidebar-foreground hover:bg-sidebar-accent/60 hover:text-sidebar-accent-foreground'
              "
            >
              <span v-html="item.icon" class="shrink-0 opacity-80" />
              {{ item.label }}
            </Link>
          </template>
        </nav>
      </aside>

      <!-- Main content -->
      <main class="ml-56 min-h-[calc(100vh-3.5rem)] flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
