<script setup lang="ts">
import { computed } from "vue";

type DieTone = "muted" | "primary" | "secondary";

interface Props {
    value: number;
    tone?: DieTone;
}

const props = withDefaults(defineProps<Props>(), {
    tone: "muted",
});

/** Pip slots on a 3x3 grid (0 = top-left, 8 = bottom-right) for each d6 face. */
const pipLayouts: Record<number, number[]> = {
    1: [4],
    2: [2, 6],
    3: [2, 4, 6],
    4: [0, 2, 6, 8],
    5: [0, 2, 4, 6, 8],
    6: [0, 2, 3, 5, 6, 8],
};

const pips = computed<{ x: number; y: number }[]>(() =>
    (pipLayouts[props.value] ?? []).map((slot: number) => ({
        x: 4 + (slot % 3) * 4,
        y: 4 + Math.floor(slot / 3) * 4,
    }))
);

const faceClasses: Record<DieTone, string> = {
    muted: "fill-base-content/6 stroke-base-content/20",
    primary: "fill-primary/15 stroke-primary/70",
    secondary: "fill-secondary/15 stroke-secondary/70",
};

const pipClasses: Record<DieTone, string> = {
    muted: "fill-base-content/55",
    primary: "fill-primary",
    secondary: "fill-secondary",
};
</script>

<template>
    <svg v-if="pips.length" viewBox="0 0 16 16" class="size-5 shrink-0" role="img" :aria-label="`Dado: ${value}`">
        <rect x="0.5" y="0.5" width="15" height="15" rx="3.5" stroke-width="1" :class="faceClasses[tone]" />
        <circle v-for="pip, index in pips" :key="index" :cx="pip.x" :cy="pip.y" r="1.4" :class="pipClasses[tone]" />
    </svg>
    <span
        v-else
        class="inline-flex h-5 min-w-5 items-center justify-center rounded-sm bg-base-content/8 px-1 font-mono text-[0.7rem] tabular-nums"
    >{{ value }}</span>
</template>
