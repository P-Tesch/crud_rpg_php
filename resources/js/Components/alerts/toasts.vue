<script setup lang="ts">
import { ref } from "vue";
import Icon from "@ui/Icon.vue";

export interface ToastMessage {
    message: string,
    isError: boolean
}

interface Props {
    messages: ToastMessage[];
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
    <div v-if="props.visible" class="toast toast-bottom toast-start pointer-events-none z-50 w-full max-w-sm">
        <div
            v-for="toast, key in messages"
            :key="key"
            role="alert"
            class="flex items-start gap-3 rounded-box border border-l-2 bg-base-200/95 px-4 py-3 text-sm shadow-xl backdrop-blur-md"
            :class="toast.isError ? 'border-error/30 border-l-error' : 'border-success/30 border-l-success'"
        >
            <Icon :name="toast.isError ? 'alert' : 'check-circle'" class="mt-0.5 size-4.5" :class="toast.isError ? 'text-error' : 'text-success'" />
            <span class="whitespace-normal">{{ toast.message }}</span>
        </div>
    </div>
</template>
