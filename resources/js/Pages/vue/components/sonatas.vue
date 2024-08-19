<script setup lang="ts">
import { toRaw } from "vue";
import { Sheet, SonataArray } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalSonatas: SonataArray = structuredClone(toRaw(props.sheet.sonatas));

const emit = defineEmits(["sync", "add", "addAbility"]);

function rollSonata(sonataName: string | number, abilityName: number) {
    // TODO
}

function isOriginal(key: string | number) : boolean {
    return !originalSonatas.hasOwnProperty(key);
}

function remove(key: string | number) : void {
    delete props.sheet.sonatas[key];
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 flex flex-col justify-between">
        <div>
            <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
                <h1 class="font-semibold text-2xl">Sonatas</h1>
            </div>
            <div class="overflow-auto">
                <div v-for="sonata, key in sheet.sonatas" class="collapse collapse-arrow bg-base-100">
                    <button v-if="isOriginal(key)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="remove(key)">✕</button>
                    <input type="checkbox" name="sonatas-collapse" />
                    <div class="collapse-title text-xl font-medium">{{ key }}</div>
                    <div class="collapse-content">
                        <div v-for="ability, k in sonata.sonata_abilities" class="collapse collapse-arrow bg-base-100">
                            <input type="checkbox" name="sonata-abilities-collapse" />
                            <div class="collapse-title text-xl font-medium">{{ ability.name }} [{{ ability.level }}]</div>
                            <div class="collapse-content">
                                <p>Descrição: {{ ability.description }}</p>
                                <button class="btn btn-outline btn-secondary btn-sm" @click="rollSonata(key, k)">Rolar</button>
                            </div>
                        </div>
                        <div class="w-full text-center">
                            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
