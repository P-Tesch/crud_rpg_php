<script setup lang="ts">
import SectionCard from '@ui/SectionCard.vue';
import Stepper from '@ui/Stepper.vue';
import PipMeter from '@ui/PipMeter.vue';
import type { Sheet, Stats, AssociativeArray } from 'rpgTypes';


interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const originalStats: Stats = Object.assign({}, props.sheet.stats);

const stats: AssociativeArray = {
    "strength": "Força",
    "dexterity": "Destreza",
    "agility": "Agilidade",
    "endurance": "Resistência",
    "charisma": "Carisma",
    "perception": "Percepção",
    "intelligence": "Inteligência",
    "magic": "Magia",
    "tech": "Tech",
    "lineage": "Linhagem",
    "blood": "Sangue",
    "faith": "Fé"
}

function increase(key: string | number) : void {
    props.sheet.stats[key]++;
}

function decrease(key: string | number) : void {
    props.sheet.stats[key]--;
}

function maxFor(key: string | number) : number {
    switch (key) {
        case "tech":
        case "lineage":
        case "blood":
        case "beast":
        case "magic":
        case "faith":
            return 10;
        default:
            return props.sheet.classes.isVampire ? 7 : 5;
    }
}

function canIncrease(key: string | number, value: number) : boolean {
    return value < maxFor(key);
}

function canDecrease(key: string | number, value: number) : boolean {
    return originalStats[key] < value;
}

</script>

<template>
    <SectionCard title="Estatísticas">
        <ul class="flex flex-col">
            <li v-for="stat, key in sheet.stats" :key="key" class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                <span class="min-w-0 flex-1 truncate text-sm font-medium">{{ stats[key] ?? key }}</span>
                <PipMeter :value="stat" :max="maxFor(key)" />
                <span class="w-7 text-right font-mono text-lg font-semibold tabular-nums">{{ stat }}</span>
                <Stepper
                    :label="stats[key]"
                    :can-increase="canIncrease(key, stat)"
                    :can-decrease="canDecrease(key, stat)"
                    @increase="increase(key)"
                    @decrease="decrease(key)"
                />
            </li>
        </ul>
    </SectionCard>
</template>
