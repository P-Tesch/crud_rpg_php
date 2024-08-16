<script setup lang="ts">
import { ref } from "vue";
import FailToast from './components/alerts/failToast.vue';

const messages = ref<string[]>([]);

window.onerror = (msg: string | Event, url: string | undefined, line: number | undefined, col: number | undefined, error: Error | undefined) : void => {
    if (typeof msg == "string") {
        showError(msg);
    }
}

window.addEventListener(
    "unhandledrejection",
    (event) => {
        let error: Error = event.reason;
        showError(error.message);
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
        <FailToast :messages :visible="true" />
    </div>
</template>
