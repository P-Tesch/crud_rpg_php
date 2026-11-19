<script setup lang="ts">
import { computed } from "vue";
import Icon from "@ui/Icon.vue";

interface Props {
    title: string;
    subtitle?: string;
    size?: "sm" | "md" | "lg";
}

const props = withDefaults(defineProps<Props>(), {
    subtitle: "",
    size: "md",
});

const sizeClass = computed<string>(() => ({
    sm: "sm:max-w-md",
    md: "sm:max-w-2xl",
    lg: "sm:max-w-4xl",
})[props.size]);
</script>

<!-- Rendered inside a component's own <dialog class="modal">, so the dialog ref stays with that component. -->
<template>
    <div class="modal-box hud-frame chamfer w-full p-px shadow-2xl" :class="sizeClass">
        <div class="chamfer flex max-h-[calc(100dvh-5rem)] flex-col bg-base-200">
            <header class="flex items-start gap-3 border-b border-base-content/8 px-5 py-4">
                <span class="mt-1.5 h-4 w-0.5 shrink-0 bg-primary"></span>
                <div class="min-w-0">
                    <h3 class="font-display text-lg font-semibold tracking-tight">{{ title }}</h3>
                    <p v-if="subtitle" class="label-mono mt-0.5">{{ subtitle }}</p>
                </div>
                <form method="dialog" class="ml-auto">
                    <button class="btn btn-square btn-ghost btn-sm text-base-content/60" aria-label="Fechar">
                        <Icon name="x" />
                    </button>
                </form>
            </header>
            <div class="overflow-y-auto px-5 py-4">
                <slot />
            </div>
            <footer v-if="$slots.footer" class="flex justify-end gap-2 border-t border-base-content/8 px-5 py-3">
                <slot name="footer" />
            </footer>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop bg-base-100/70 backdrop-blur-[2px]">
        <button>fechar</button>
    </form>
</template>
