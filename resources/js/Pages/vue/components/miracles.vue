<script setup lang="ts">
import type { Miracle, Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalMiracles: Miracle[] = Object.assign({}, props.sheet.miracles);

const emit = defineEmits(["add"]);

function isOriginal(miracle: Miracle) : boolean {
    let originalArray = Object.values(originalMiracles);
    let isOriginal: boolean = false;
    originalArray.forEach(
        (original) => {
            if (original.name == miracle.name) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Milagres</h1>
        </div>

        <div v-for="miracle, key in sheet.miracles" class="collapse collapse-arrow bg-base-100">
            <button v-if="isOriginal(miracle)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="sheet.miracles.splice(key, 1)">✕</button>
            <input type="checkbox" name="miracles-collapse" />
            <div class="collapse-title text-xl font-medium">{{ miracle.name }}</div>
            <div class="collapse-content">
                <p>{{ miracle.description }}</p>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
