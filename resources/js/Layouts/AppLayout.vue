<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import BrandMark from "@ui/BrandMark.vue";
import Icon from "@ui/Icon.vue";
import ThemeToggle from "@ui/ThemeToggle.vue";

const isAsideOpen = ref<boolean>(false);

function logout() : void {
    router.get("/api/logout");
}
</script>

<template>
    <div class="drawer drawer-end lg:drawer-open">
        <input id="app-aside" v-model="isAsideOpen" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex min-h-dvh flex-col">
            <header class="sticky top-0 z-30 border-b border-base-content/10 bg-base-100/75 backdrop-blur-md">
                <div class="flex h-16 items-center gap-2 px-4 sm:px-6">
                    <div class="flex items-center gap-2.5">
                        <BrandMark />
                        <p class="hidden font-display text-base leading-none font-semibold tracking-tight sm:block">Ficha</p>
                    </div>

                    <div class="ml-auto flex items-center gap-1.5">
                        <slot name="actions" />
                        <span class="mx-1 h-6 w-px bg-base-content/10"></span>
                        <ThemeToggle />
                        <label for="app-aside" class="btn btn-square btn-ghost btn-sm text-base-content/70 lg:hidden" aria-label="Abrir rolagens" title="Rolagens">
                            <Icon name="history" />
                        </label>
                        <button type="button" class="btn btn-square btn-ghost btn-sm text-base-content/70 hover:text-error" aria-label="Sair" title="Sair" @click="logout()">
                            <Icon name="logout" />
                        </button>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:px-6 lg:py-8">
                <slot />
            </main>
        </div>

        <div class="drawer-side z-40">
            <label for="app-aside" aria-label="Fechar rolagens" class="drawer-overlay"></label>
            <aside class="flex h-dvh w-[22rem] max-w-[88vw] flex-col border-l border-base-content/10 bg-base-200/95 backdrop-blur-md">
                <slot name="aside" />
            </aside>
        </div>
    </div>
</template>
