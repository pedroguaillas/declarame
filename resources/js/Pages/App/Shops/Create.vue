<script setup>

// Imoprts
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';

let date = new Date()
date.setHours(date.getHours() - 5)
let max = date.toISOString().substring(0, 10)

const form = useForm({
    serie: '',
    date: '',
    contact_id: null,
    autorization: ''
});

const error = reactive({})

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
                        <InputLabel for="date" value="Fecha de emision" />
                        <TextInput v-model="form.date" type="date" class="mt-2 block w-full" :min="'2015-01-01'" :max="max" />
                        <InputError :message="error.date" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="serie" value="N° de serie" />
                        <TextInput v-model="form.serie" type="text" class="mt-2 block w-full" :maxlength="17" />
                        <InputError :message="error.serie" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="serie" value="Proveedor" />
                        <TextInput v-model="form.contact_id" type="text" class="mt-2 block w-full" />
                        <InputError :message="error.contact_id" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="autorization" value="Autorizacion" />
                        <TextInput v-model="form.autorization" type="text" class="mt-2 block w-full" :maxlength="49" />
                        <InputError :message="error.autorization" />
                    </div>
                </div>

                <div class="w-[49%]">

                    <div class="mt-2">
                        <InputLabel for="date" value="Fecha de emision" />
                        <TextInput v-model="form.date" type="date" class="mt-2 block w-full" />
                        <InputError :message="error.date" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="serie" value="N° de serie" />
                        <TextInput v-model="form.serie" type="text" class="mt-2 block w-full" />
                        <InputError :message="error.serie" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="serie" value="Proveedor" />
                        <TextInput v-model="form.contact_id" type="text" class="mt-2 block w-full" />
                        <InputError :message="error.contact_id" />
                    </div>

                    <div class="mt-2">
                        <InputLabel for="autorization" value="Autorizacion" />
                        <TextInput v-model="form.autorization" type="text" class="mt-2 block w-full" />
                        <InputError :message="error.autorization" />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>