<script setup lang="ts">
import { ref } from "vue";
import FailToast from './components/alerts/failToast.vue';

const messages = ref<string[]>([]);

window.onerror = (msg: string | Event, url: string | undefined, line: number | undefined, col: number | undefined, error: Error | undefined) : void => {
    const errorMessage = error?.message;
    if (errorMessage != null) {
        showError(errorMessage);
    }

    logError(url, line, col, msg);
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

function logError(url: string | undefined, line: number | undefined, col: number | undefined, msg: string | Event) : void {
    let message: string;
    if (msg instanceof Event) {
        message = msg.type;
    } else {
        message = msg;
    }

    console.error(
        "URL: " + url + "\n" +
        "Line: " + line + "\n" +
        "Column: " + col + "\n" +
        "Error: " + message
    );
}

</script>

<template>
    <div>
        <FailToast :messages :visible="true" />
    </div>
</template>
