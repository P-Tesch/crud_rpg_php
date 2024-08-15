<script setup lang="ts">
import type { Sheet, Advantage } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props: Props = defineProps<Props>();

const originalAdvantages: Advantage[] = Object.values(Object.assign({}, props.sheet.advantages));

const emit = defineEmits(["add"]);

function isOriginal(advantage: Advantage) : boolean {
    let isOriginal: boolean = false;
    originalAdvantages.forEach(
        (original) => {
            if (original.name == advantage.name && original.level == advantage.level) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

function remove(key: number, toRemove: Advantage) : void {
    let isPresent: boolean = false;
    originalAdvantages.forEach(
        (original) => {
            if (toRemove.name == original.name) {
                isPresent = true;
                props.sheet.advantages[key] = original;
            }
        }
    );

    if (!isPresent) {
        props.sheet.advantages.splice(key, 1);
    }
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Vantagens</h1>
        </div>

        <div v-for="advantage, key in sheet.advantages" class="collapse collapse-arrow bg-base-100">
            <button v-if="isOriginal(advantage)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="remove(key, advantage)">✕</button>
            <input type="checkbox" name="advantages-collapse" />
            <div class="collapse-title text-xl font-medium">{{ advantage.name }}</div>
            <div class="collapse-content">
                <p>{{ advantage.description }}</p>
                <p>Level: {{ advantage.level }}</p>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
