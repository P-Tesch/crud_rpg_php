<script setup lang="ts">

import { router } from '@inertiajs/vue3';

const emit = defineEmits(["persist", "end-turn", "roll-generic"]);

function logout() : void {
    router.get("api/logout");
}

</script>

<template>
    <div class="w-full h-[8%] absolute">
    </div>
    <div class="border border-1 rounded-md border-primary border-x-0 border-b-0 bg-base-100 w-full fixed bottom-0 z-10 h-[8%]">
        <div class="float-left ml-[2%] space-x-5 mt-[0.5%]">
            <button class="btn btn-outline btn-error z-20" @click="logout()" >Sair</button>
            <div class="dropdown dropdown-top z-20">
                <div tabindex="0" role="button" class="btn btn-outline btn-accent">
                    Tema
                </div>
                <ul tabindex="0" class="dropdown-content bg-base-300 rounded-box z-30">
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Escuro" value="dark" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Claro" value="light" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Cyberpunk" value="cyberpunk" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Retro" value="retro" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Dracula" value="dracula" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Esmeralda" value="emerald" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Noite" value="night" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Nórdico" value="nord" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Pastel" value="pastel" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Lofi" value="lofi" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Wireframe" value="wireframe" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Preto" value="black" />
                    </li>
                    <li>
                        <input type="radio" name="theme-dropdown" class="theme-controller btn btn-sm btn-block btn-ghost justify-start" aria-label="Floresta" value="forest" />
                    </li>
                </ul>
            </div>
            <button class="btn btn-outline btn-secondary z-20" @click="$emit('roll-generic')">Rolar</button>
        </div>
        <div class="float-right mr-[2%] space-x-5 mt-[0.5%]">
            <button class="btn btn-outline btn-accent z-20" @click="$emit('persist')">Salvar</button>
            <button class="btn btn-outline btn-secondary z-20" @click="$emit('end-turn')">Terminar turno</button>
        </div>
    </div>
</template>
