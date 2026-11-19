<script setup lang="ts">
import Icon from "@ui/Icon.vue";

interface Props {
    title: string;
    cost?: number | string | null;
    level?: number | null;
}

withDefaults(defineProps<Props>(), {
    cost: null,
    level: null,
});

defineEmits<{
    add: [];
}>();
</script>

<template>
    <article class="group flex flex-col gap-3 rounded-box border border-base-content/10 bg-base-100/60 p-4 transition-colors hover:border-primary/40">
        <header class="flex flex-wrap items-start gap-x-3 gap-y-1">
            <h4 class="font-display text-base font-semibold tracking-tight">{{ title }}</h4>
            <div class="ml-auto flex items-center gap-1.5">
                <slot name="meta" />
                <span v-if="level != null" class="badge badge-sm badge-ghost font-mono">Nv {{ level }}</span>
                <span v-if="cost != null" class="badge badge-sm badge-soft badge-primary h-auto font-mono">{{ typeof cost === "number" ? `${cost} pts` : cost }}</span>
            </div>
        </header>
        <div class="flex flex-col gap-2 text-sm text-base-content/75">
            <slot />
        </div>
        <button type="button" class="btn btn-sm btn-primary mt-auto self-end" @click="$emit('add')">
            <Icon name="plus" class="size-3.5" />
            Adicionar
        </button>
    </article>
</template>
