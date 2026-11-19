<script setup lang="ts">
import Icon from "@ui/Icon.vue";

interface Props {
    canIncrease?: boolean;
    canDecrease?: boolean;
    label?: string;
}

withDefaults(defineProps<Props>(), {
    canIncrease: true,
    canDecrease: true,
    label: "",
});

defineEmits<{
    increase: [event: MouseEvent];
    decrease: [event: MouseEvent];
}>();
</script>

<template>
    <div class="flex items-center gap-1">
        <button
            type="button"
            class="btn btn-square btn-xs btn-ghost border-base-content/15 text-base-content/70 hover:border-base-content/30 hover:text-base-content"
            :class="{ invisible: !canDecrease }"
            :aria-label="`Diminuir ${label}`"
            @click="$emit('decrease', $event)"
        >
            <Icon name="minus" class="size-3.5" />
        </button>
        <button
            type="button"
            class="btn btn-square btn-xs btn-soft btn-primary"
            :class="{ invisible: !canIncrease }"
            :aria-label="`Aumentar ${label}`"
            @click="$emit('increase', $event)"
        >
            <Icon name="plus" class="size-3.5" />
        </button>
    </div>
</template>
