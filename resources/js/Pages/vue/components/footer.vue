<script setup lang="ts">

import { router } from '@inertiajs/vue3';

const emit = defineEmits(["persist", "end-turn", "roll-generic"]);

function logout() : void {
    router.get("api/logout");
}

function themeSelect() : void {

}

</script>

<template>
    <div class="w-full h-[8%] absolute">
    </div>
    <div class="border border-1 rounded-md border-primary border-x-0 border-b-0 bg-base-100 w-full fixed bottom-0 z-10 h-[8%]">
        <div class="float-left ml-[2%] space-x-5 mt-[0.5%]">
            <button class="btn btn-outline btn-error z-20" @click="logout()" >Sair</button>
            <button class="btn btn-outline btn-accent z-20" @click="themeSelect()">Tema</button>
            <button class="btn btn-outline btn-secondary z-20" @click="$emit('roll-generic')">Rolar</button>
        </div>
        <div class="float-right mr-[2%] space-x-5 mt-[0.5%]">
            <button class="btn btn-outline btn-accent z-20" @click="$emit('persist')">Salvar</button>
            <button class="btn btn-outline btn-secondary z-20" @click="$emit('end-turn')">Terminar turno</button>
        </div>
    </div>
</template>
