<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

defineProps({ sheet: Object });

const emit = defineEmits("add");

async function rollMysticEye(item) {
    /// TODO
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Olhos místicos</h1>
        </div>

        <div v-for="value, key in sheet.mysticEyes" class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="mystic-eyes-collapse" />
            <div class="collapse-title text-xl font-medium">{{ value.name }}</div>
            <div class="collapse-content">
                <div v-if="value.passive != null" class="collapse collapse-arrow bg-base-100">
                    <input type="checkbox" name="me-passive-collapse" />
                    <div class="collapse-title text-xl font-medium">Passivo</div>
                    <div class="collapse-content">
                        <p>{{ value.passive }}</p>
                    </div>
                </div>
                <div v-if="value.active != null" class="collapse collapse-arrow bg-base-100">
                    <input type="checkbox" name="me-passive-collapse" />
                    <div class="collapse-title text-xl font-medium">Ativo</div>
                    <div class="collapse-content grid grid-flow-row grid-cols-2">
                        <p class="col-start-1 row-start-1" >{{ value.active }}</p>
                        <p class="col-start-1 row-start-2">Cooldown: {{ value.pivot.current_cooldown }} / {{ value.cooldown }}</p>
                        <button class="btn btn-outline btn-secondary btn-sm grid-rows-1 col-start-2 row-span-full w-4/12 ml-auto my-auto mr-5" @click="rollMysticEye(value)">Roll</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full" @click="this.$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
