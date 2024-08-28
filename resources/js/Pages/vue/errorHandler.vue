<script setup lang="ts">
import { ref } from "vue";
import FailToast from '@alerts/failToast.vue';
import ToastError from "@scripts/ToastError.ts";

const messages = ref<string[]>([]);

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
    messages.value.push(msg);

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
        <FailToast :messages :visible="true" class="z-50"/>
    </div>
</template>
