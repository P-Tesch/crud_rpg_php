<script setup lang="ts">
import { ref } from "vue";
import Icon from "@ui/Icon.vue";

interface Props {
    title?: string;
    defaultOpen?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: "",
    defaultOpen: false,
});

const isOpen = ref<boolean>(props.defaultOpen);
</script>

<template>
    <div class="rounded-field border border-base-content/8 bg-base-100/50 transition-colors" :class="{ 'border-base-content/15': isOpen }">
        <div class="flex min-h-10 items-center gap-2 pr-2">
            <button
                type="button"
                class="flex min-w-0 flex-1 items-center gap-2 py-2 pl-2.5 text-left"
                :aria-expanded="isOpen"
                @click="isOpen = !isOpen"
            >
                <Icon name="chevron-right" class="size-3.5 text-base-content/50 transition-transform" :class="{ 'rotate-90 text-primary': isOpen }" />
                <span class="truncate font-medium"><slot name="title">{{ title }}</slot></span>
                <span v-if="$slots.meta" class="flex shrink-0 items-center gap-1.5"><slot name="meta" /></span>
            </button>
            <div v-if="$slots.actions" class="flex shrink-0 items-center gap-1">
                <slot name="actions" />
            </div>
        </div>
        <div v-show="isOpen" class="flex flex-col gap-2 px-3 pb-3 pl-8 text-sm text-base-content/80">
            <slot />
        </div>
    </div>
</template>
