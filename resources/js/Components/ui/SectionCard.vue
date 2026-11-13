<script setup lang="ts">
interface Props {
    title: string;
}

defineProps<Props>();
</script>

<template>
    <section class="hud-frame chamfer p-px">
        <div class="chamfer flex h-full flex-col bg-base-200/90 backdrop-blur-sm">
            <header class="flex min-h-12 items-center gap-3 border-b border-base-content/8 px-4 py-2.5">
                <span class="h-3.5 w-0.5 bg-primary"></span>
                <h2 class="font-display text-[0.95rem] font-semibold tracking-tight">{{ title }}</h2>
                <div v-if="$slots.actions" class="ml-auto flex items-center gap-2 pr-2">
                    <slot name="actions" />
                </div>
            </header>
            <div class="flex-1 p-2">
                <slot />
            </div>
            <footer v-if="$slots.footer" class="px-3 pb-3">
                <slot name="footer" />
            </footer>
        </div>
    </section>
</template>
