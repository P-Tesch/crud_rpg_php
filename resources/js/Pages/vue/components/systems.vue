<script setup lang="ts">
import { toRaw } from "vue";
import type { Sheet, SonataAbility, SonataArray, Subsystem, SystemArray } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalSystems: SystemArray = structuredClone(toRaw(props.sheet.systems));

const emit = defineEmits(["sync", "add", "addSubsystem"]);

function isOriginal(key: string | number) : boolean {
    return !originalSystems.hasOwnProperty(key);
}

function isOriginalSubsystem(SystemKey: string | number, subsystem: Subsystem) : boolean {
    let original = true;
    originalSystems[SystemKey]?.subsystems.forEach((entry) => {
        if (entry.name == subsystem.name) {
            original = false;
        }
    });
    return original;
}

function remove(key: string | number) : void {
    delete props.sheet.systems[key];
}

function removeSubsystem(systemKey: string | number, subsystemKey: number) : void {
    props.sheet.systems[systemKey].subsystems.splice(subsystemKey, 1);

}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 flex flex-col justify-between">
        <div>
            <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
                <h1 class="font-semibold text-2xl">Sistemas</h1>
            </div>
            <div class="overflow-auto">
                <div v-for="system, key in sheet.systems" class="collapse collapse-arrow bg-base-100">
                    <button class="btn btn-sm btn-outline btn-accent absolute top-4 right-44">Ativar</button>
                    <button class="btn btn-sm btn-outline btn-error absolute top-4 right-20">Queimar</button>
                    <button v-if="isOriginal(key)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="remove(key)">✕</button>
                    <input type="checkbox" name="systems-collapse" />
                    <div class="collapse-title text-xl font-medium">{{ key }}</div>
                    <div class="collapse-content">
                        <div v-for="subsystem, k in system.subsystems" class="collapse collapse-arrow bg-base-100">
                            <button v-if="isOriginalSubsystem(key, subsystem)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="removeSubsystem(key, k)">✕</button>
                            <input type="checkbox" name="subsystems-collapse" />
                            <span class="collapse-title text-xl font-medium">{{ subsystem.name }}</span>
                            <div class="collapse-content flex flex-col">
                                <p class="">Descrição: {{ subsystem.description }}</p>
                                <div class="flex flex-row-reverse mt-2">
                                    <div class="flex flex-row-reverse">
                                        <button class="btn btn-outline btn-secondary btn-sm ml-auto">Rolar</button>
                                        <button class="btn btn-outline btn-error btn-sm ml-auto mr-3">Queimar</button>
                                        <button class="btn btn-outline btn-accent btn-sm ml-auto mr-3">Ativar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full text-center">
                            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('addSubsystem', key)">Adicionar</button>
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
