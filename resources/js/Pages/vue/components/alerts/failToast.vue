<script setup lang="ts">
import { ref } from "vue";

interface Props {
    messages: string[];
    visible?: boolean;
}

const props = withDefaults(
    defineProps<Props>(),
    {
        visible: false
    }
);

const toastRef = ref<boolean>(props.visible);

defineExpose({toastRef});

</script>

<template>
    <div v-if="props.visible" class="toast toast-bottom toast-start w-1/5 ml-3">
        <div role="alert" class="alert alert-error w-full h-fit" v-for="message, key in messages">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 shrink-0 stroke-current"
                fill="none"
                viewBox="0 0 24 24">
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="w-full">{{ message }}</span>
        </div>
    </div>
</template>
