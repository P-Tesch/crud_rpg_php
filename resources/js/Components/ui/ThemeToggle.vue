<script setup lang="ts">
import { ref } from "vue";
import Icon from "@ui/Icon.vue";

type ThemeName = "noturno" | "aurora";

const theme = ref<ThemeName>(document.documentElement.getAttribute("data-theme") === "aurora" ? "aurora" : "noturno");

function toggleTheme() : void {
    theme.value = theme.value === "noturno" ? "aurora" : "noturno";
    document.documentElement.setAttribute("data-theme", theme.value);

    try {
        localStorage.setItem("theme", theme.value);
    } catch (e) {
        // Storage can be unavailable (private mode); the theme still applies for this visit.
    }
}
</script>

<template>
    <button
        type="button"
        class="btn btn-square btn-ghost btn-sm text-base-content/70"
        :aria-label="theme === 'noturno' ? 'Usar tema claro' : 'Usar tema escuro'"
        :title="theme === 'noturno' ? 'Tema claro' : 'Tema escuro'"
        @click="toggleTheme()"
    >
        <Icon :name="theme === 'noturno' ? 'sun' : 'moon'" />
    </button>
</template>
