<script setup lang="ts">
interface Props {
    value: number;
    max: number;
}

const props = defineProps<Props>();

function isFilled(pip: number) : boolean {
    return pip > props.max - props.value;
}
</script>

<template>
    <span class="hidden items-center gap-0.5 sm:flex" aria-hidden="true">
        <span
            v-for="pip in Math.max(0, max)"
            :key="pip"
            class="h-2.5 w-1.5 rounded-[1px]"
            :class="isFilled(pip) ? 'bg-primary' : 'bg-base-content/10'"
        ></span>
    </span>
</template>
