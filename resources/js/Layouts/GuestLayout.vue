<script setup lang="ts">
import ThemeToggle from "@ui/ThemeToggle.vue";
</script>

<template>
    <div class="relative flex min-h-dvh items-center justify-center px-4 py-10">
        <div class="absolute top-4 right-4">
            <ThemeToggle />
        </div>
        <div class="w-full max-w-sm">
            <slot />
        </div>
    </div>
</template>
