<script setup lang="ts">
import { Form } from "vee-validate";

interface Props {
    initialValues?: Record<string, any>;
    validationSchema?: any; // opcional: suporte futuro a Yup/Zod
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: "submit", values: Record<string, any>, actions: any): void
}>();

function onSubmit(values: Record<string, any>, actions: any) {
    emit("submit", values, actions);
}
</script>

<template>
    <Form 
        :initial-values="props.initialValues"
        :validation-schema="props.validationSchema"
        @submit="onSubmit"
    >
        <slot />
    </Form>
</template>
