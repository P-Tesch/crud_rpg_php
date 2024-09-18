<script setup lang="ts">
import { ref } from "vue";
import Toasts, { type ToastMessage } from '@alerts/toasts.vue';
import ToastError from "@scripts/ToastError.ts";

const messages = ref<ToastMessage[]>([]);

defineExpose({showSuccess});

window.onerror = (msg: string | Event, url: string | undefined, line: number | undefined, col: number | undefined, error: Error | undefined) : boolean => {
    const errorMessage = error?.message;

    if (errorMessage != null) {
        if (error instanceof ToastError) {
            showError(errorMessage);
            return true;
        }

        window.document.body.innerHTML = errorMessage;
    }

    return false;
}

window.addEventListener(
    "unhandledrejection",
    (event) => {
        let error: Error = event.reason;

        if (error instanceof ToastError) {
            showError(error.message);
            return true;
        }

        window.document.body.innerHTML = error.message;

        return false;
    }
);

function showError(msg: string) : void {
    show(msg, true);
}

function showSuccess(msg: string) : void {
    show(msg, false);
}

function show(msg:string, isError: boolean) : void {
    messages.value.push({message: msg, isError: isError});

    setTimeout(
        () : void => {
            messages.value.shift();
        },
        3500
    );
}


</script>

<template>
    <div>
        <Toasts :messages :visible="true" class="z-50"/>
    </div>
</template>
