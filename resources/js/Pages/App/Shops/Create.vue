<script setup>

// Imoprts
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { reactive, ref, watch } from 'vue';

let dateString = new Date()
dateString.setHours(dateString.getHours() - 5)
let max = dateString.toISOString().substring(0, 10)

const voucherTypes = [
    { id: 1, description: 'Factura' },
    { id: 2, description: 'Nota de credito' },
    { id: 3, description: 'Nota de venta' },
    { id: 4, description: 'Liquidacion en compra' },
];

// Refs
const voucher_type = ref('')
const date = ref('')
const serie = ref('')
const autorization = ref('')
const id = ref('')
const type_id = ref('')
const contact_id = ref('')

// Reactives
const error = reactive({})

// Watches
watch(id, () => {
    if (id.value.length === 10 || id.value.length === 13) {
        console.log(id.value)
    }
})

</script>

<template>
    <AdminLayout title="Nueva compra">

        <!-- Card -->
        <div class="p-4 bg-white dark:bg-slate-800 rounded drop-shadow-md">

            <h4 class="mb-4 text-sm dark:text-slate-300 sm:text-lg text-center font-bold">Registrar compra</h4>

            <!-- Row -->
            <div class="flex justify-between">

                <!-- Col -->
                <div class="w-[49%]">

                    <div class="mt-2">
                        <InputLabel for="voucher_type" value="Tipo de comprobante" />
                        <!-- Type voucher Dropdown -->
                        <div class="mt-2 block w-full">
                            <select
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option v-for="vt, i in voucherTypes" :key="`vt${i}`">{{ vt.description }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2">
                        <InputLabel for="date" value="Fecha de emision" />
                        <TextInput v-model="date" type="date" class="mt-2 block w-full" :min="'2015-01-01'"
                            :max="max" />
                        <InputError :message="error.date" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="serie" value="N° de serie" />
                        <TextInput v-model.trim="serie" type="text" class="mt-2 block w-full" :maxlength="17" />
                        <InputError :message="error.serie" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="autorization" value="Autorizacion" />
                        <TextInput v-model.trim="autorization" type="text" class="mt-2 block w-full" :maxlength="49" />
                        <InputError :message="error.autorization" />
                    </div>
                </div>

                <div class="w-[49%]">

                    <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg">

                        <div class="m-2">
                            <InputLabel for="type_id" value="Tipo de identificacion" />
                            <TextInput v-model.trim="type_id" type="text" class="mt-2 block w-full" :maxlength="13" />
                            <InputError :message="error.type_id" />
                        </div>

                        <div class="m-2">
                            <InputLabel for="contact_id" value="Proveedor" />
                            <TextInput v-model.trim="contact_id" type="text" class="mt-2 block w-full"
                                :maxlength="13" />
                            <InputError :message="error.contact_id" />
                        </div>

                        <div
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600">
                            <div>
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-semibold">
                                        Pedro Guaillas
                                    </div>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 text-start">
                                    1105167694
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>