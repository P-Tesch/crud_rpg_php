<script setup lang="ts">
import { onBeforeUnmount, ref, useId } from "vue";

interface Props {
    text: string;
}

defineProps<Props>();

const hintId = useId();
const anchor = ref<HTMLElement>();
const isOpen = ref<boolean>(false);
const position = ref<{ top: number; left: number; placeAbove: boolean }>({ top: 0, left: 0, placeAbove: false });

/** Rough height of a multi-line hint; used to flip it above the anchor near the bottom of the viewport. */
const estimatedHintHeight = 140;

function open() : void {
    if (anchor.value == undefined) {
        return;
    }

    const rect = anchor.value.getBoundingClientRect();
    const placeAbove = rect.bottom + estimatedHintHeight > window.innerHeight;

    position.value = {
        top: placeAbove ? rect.top - 6 : rect.bottom + 6,
        left: Math.min(rect.left, window.innerWidth - 336),
        placeAbove,
    };
    isOpen.value = true;
    window.addEventListener("scroll", close, { capture: true, once: true });
}

function close() : void {
    isOpen.value = false;
    window.removeEventListener("scroll", close, { capture: true });
}

onBeforeUnmount(close);
</script>

<!-- Teleported to body so card clip-paths (chamfered corners) never cut the hint off. -->
<template>
    <span
        ref="anchor"
        tabindex="0"
        class="cursor-help rounded-sm outline-none focus-visible:outline-1 focus-visible:outline-offset-2 focus-visible:outline-primary"
        :aria-describedby="isOpen ? hintId : undefined"
        @mouseenter="open()"
        @mouseleave="close()"
        @focus="open()"
        @blur="close()"
    ><slot /></span>
    <Teleport to="body">
        <div
            v-if="isOpen"
            :id="hintId"
            role="tooltip"
            class="pointer-events-none fixed z-50 w-max max-w-80 rounded-box border border-base-content/15 bg-base-300/95 px-3 py-2 text-xs leading-relaxed text-base-content shadow-xl backdrop-blur-md"
            :class="{ '-translate-y-full': position.placeAbove }"
            :style="{ top: `${position.top}px`, left: `${Math.max(8, position.left)}px` }"
        >{{ text }}</div>
    </Teleport>
</template>
