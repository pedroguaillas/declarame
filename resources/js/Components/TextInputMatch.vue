<script setup>
import { onMounted, ref, watch } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);
const isValid = ref(true);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

watch(modelValue, (value) => {
    // Check if the input value matches the pattern
    this.isValid = /^\d{3}-\d{3}-\d{10}$/.test(value);
})

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div>
        <input ref="input"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            :value="modelValue" @input="$emit('update:modelValue', $event.target.value)">
        <p v-if="!isValid">Please enter a value in the format 000-000-0000000000</p>
    </div>
</template>
