<script setup lang="ts">
import { Sheet, Stats } from 'rpgTypes';


interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const originalStats: Stats = Object.assign({}, props.sheet.stats);

const stats = {
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

function canIncrease(key: string | number, value: number) : boolean {
    let max = 5;
    if (props.sheet.classes.isVampire) {
        max = 7;
    }

    switch (key) {
        case "tech":
        case "lineage":
        case "blood":
        case "beast":
        case "magic":
        case "faith":
            return value < 10;
        default:
            return value < max;
    }
}

function canDecrease(key: string | number, value: number) : boolean {
    return originalStats[key] < value;
}

</script>

<template>
     <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Estatísticas</h1>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Estatística</th>
                    <th class="text-center">Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="stat, key in sheet.stats">
                    <th>{{ stats[key] }}</th>
                    <th class="text-center">{{ stat }}</th>
                    <th class="text-center space-x-1">
                        <button class="btn btn-outline btn-primary btn-xs" id="{{ key }}IncreaseButton"v-if="canIncrease(key, stat)" @click="increase(key)">+</button>
                        <button class="btn btn-outline btn-accent btn-xs" id="{{ key }}DecreaseButton" v-if="canDecrease(key, stat)" @click="decrease(key)">-</button>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
