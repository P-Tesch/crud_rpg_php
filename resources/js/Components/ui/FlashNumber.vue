<script setup lang="ts">
import { onBeforeUnmount, ref, watch } from "vue";

interface Props {
    value: number;
}

const props = defineProps<Props>();

const flash = ref<"spend" | "refund" | null>(null);
let flashTimer: ReturnType<typeof setTimeout> | undefined;

watch(() => props.value, (current: number, previous: number) => {
    if (current === previous) {
        return;
    }

    flash.value = current < previous ? "spend" : "refund";
    clearTimeout(flashTimer);
    flashTimer = setTimeout(() => {
        flash.value = null;
    }, 550);
});

onBeforeUnmount(() => clearTimeout(flashTimer));
</script>

<template>
    <span
        class="inline-block origin-center transition-[color,scale] duration-300"
        :class="{
            'scale-110 text-primary': flash === 'spend',
            'scale-110 text-success': flash === 'refund',
        }"
    >{{ value }}</span>
</template>
