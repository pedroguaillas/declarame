<script setup>

// Imoprts
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { reactive } from 'vue';

// Reactives
const form = reactive({
    ruc: '',
    name: '',
    address: '',
    special_contribution: false,
    accounting: false,
    retention_agent: false,
    phantom_taxpayer: false,
    no_transactions: false,
    rimpe: '',
    phone: '',
    email: '',
    type_declaration: '',
    declaration_day: '',
    pass_sri: '',
})
const error = reactive({})

</script>

<template>
    <AdminLayout title="Nueva compra">

        <!-- Card -->
        <div class="p-4 bg-white dark:bg-slate-800 rounded drop-shadow-md">

            <h4 class="mb-4 text-sm dark:text-slate-300 sm:text-lg text-center font-bold">Registrar contribuyente</h4>

            <!-- Row -->
            <div class="flex justify-between">

                <!-- Col -->
                <div class="w-[49%]">

                    <div class="m-2">
                        <InputLabel for="ruc" value="RUC" />
                        <TextInput v-model.trim="form.ruc" type="text" class="mt-2 block w-full" :maxlength="13" />
                        <InputError :message="error.ruc" />
                    </div>

                    <div class="m-2">
                        <InputLabel for="name" value="Razon social" />
                        <TextInput v-model.trim="form.name" type="text" class="mt-2 block w-full" :maxlength="300" />
                        <InputError :message="error.name" />
                    </div>

                    <div class="m-2">
                        <InputLabel for="address" value="Direccion" />
                        <TextInput v-model.trim="form.address" type="text" class="mt-2 block w-full" :maxlength="300" />
                        <InputError :message="error.address" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="tax_type" value="Tipo de contribuyente" />
                        <!-- Type voucher Dropdown -->
                        <div class="mt-2 block w-full">
                            <select
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option>General</option>
                                <option>RIMPE Emprendedor</option>
                                <option>RIMPE Negocio Popular</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2">
                        <InputLabel for="type_declaration" value="Tipo de declaracion" />
                        <!-- Type voucher Dropdown -->
                        <div class="mt-2 block w-full">
                            <select
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option>Mensual</option>
                                <option>Semestral</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2">
                        <InputLabel for="declaration_day" value="Dia de declaracion" />
                        <TextInput v-model.trim="form.declaration_day" type="number" class="mt-2 block w-full" :min="0"
                            :max="28" />
                        <InputError :message="error.declaration_day" />
                    </div>

                </div>

                <div class="w-[49%]">

                    <div class="relative z-0 mt-1 border border-gray-200 dark:border-gray-700 rounded-lg">
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>