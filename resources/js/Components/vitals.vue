<script setup lang="ts">
import Stepper from '@ui/Stepper.vue';
import type { AssociativeArray, Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const attributes: AssociativeArray = {
    "health": "Vida",
    "initiative": "Iniciativa",
    "movement": "Movimento",
    "mana": "Mana",
    "blood_points": "Sangue",
    "blood_xp_animal": "Bxp (Animal)",
    "blood_xp_human": "Bxp (Humano)",
    "blood_xp_vampire": "Bxp (Vampiro)",
    "circuits": "Circuitos"
}

const barColors: AssociativeArray = {
    "health": "bg-primary",
    "mana": "bg-accent",
    "blood_points": "bg-error",
}

function increase(key: number | string) : void {
  props.sheet.attributes[key]++;
}

function decrease(key: number | string) : void {
  props.sheet.attributes[key]--;
}

function getMax(key: string | number) : number | undefined {
    return props.sheet.maxAttributes["max_" + key];
}

function fillPercentage(value: number, max: number | undefined) : string {
    if (max == undefined || max <= 0) {
        return "0%";
    }

    return Math.min(100, Math.max(0, (value / max) * 100)) + "%";
}

</script>

<template>
    <div class="grid grid-cols-[repeat(auto-fit,minmax(9.5rem,1fr))] gap-2">
        <div v-for="value, key in sheet.attributes" :key="key" class="rounded-field border border-base-content/8 bg-base-100/50 px-3 py-2.5">
            <p class="label-mono truncate" :title="attributes[key]">{{ attributes[key] ?? key }}</p>
            <div class="mt-1.5 flex items-end justify-between gap-2">
                <p class="font-mono leading-none tabular-nums">
                    <span class="text-2xl font-semibold">{{ value }}</span>
                    <span v-if="getMax(key) != undefined" class="text-sm text-base-content/40"> / {{ getMax(key) }}</span>
                </p>
                <Stepper :label="attributes[key]" @increase="increase(key)" @decrease="decrease(key)" />
            </div>
            <div v-if="getMax(key) != undefined" class="mt-2.5 h-1 overflow-hidden rounded-full bg-base-content/10">
                <div class="h-full rounded-full transition-[width] duration-300" :class="barColors[key] ?? 'bg-secondary'" :style="{ width: fillPercentage(value, getMax(key)) }"></div>
            </div>
        </div>

        <div v-if="sheet.scripture != null" class="rounded-field border border-base-content/8 bg-base-100/50 px-3 py-2.5">
            <p class="label-mono truncate">Escritura</p>
            <div class="mt-1.5 flex items-end justify-between gap-2">
                <p class="font-mono leading-none tabular-nums">
                    <span class="text-2xl font-semibold">{{ sheet.scripture.remaining_scripture_points }}</span>
                    <span class="text-sm text-base-content/40"> / {{ sheet.maxAttributes["max_scripture_points"] }}</span>
                </p>
                <Stepper
                    label="pontos de escritura"
                    @increase="sheet.scripture.remaining_scripture_points++"
                    @decrease="sheet.scripture.remaining_scripture_points--"
                />
            </div>
            <div class="mt-2.5 h-1 overflow-hidden rounded-full bg-base-content/10">
                <div class="h-full rounded-full bg-secondary transition-[width] duration-300" :style="{ width: fillPercentage(sheet.scripture.remaining_scripture_points, sheet.maxAttributes['max_scripture_points']) }"></div>
            </div>
        </div>
    </div>
</template>
