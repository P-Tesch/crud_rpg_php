<script setup lang="ts">
import type { Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const attributes = {
    "health": "Vida",
    "initiative": "Iniciativa",
    "movement": "Movimento",
    "mana": "Mana",
    "blood_points": "Sangue",
    "blood_xp_animal": "Bxp (Animal)",
    "blood_xp_human": "Bxp (Humano)",
    "blood_xp_vampire": "Bxp (Vampiro)"
}

function increase(key: number | string) : void {
  props.sheet.attributes[key]++;
}

function decrease(key: number | string) : void {
  props.sheet.attributes[key]--;
}

function getMax(key: string | number) : number | string {
    let max = props.sheet.maxAttributes["max_" + key];
    if (max != undefined) {
        return max;
    } else {
        return "-";
    }
}

</script>

<template>
    <div class="overflow-auto max-h-72 border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Atributos</h1>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Atributo</th>
                    <th class="text-center">Atual</th>
                    <th class="text-center">Máximo</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="value, key in sheet.attributes">
                    <th>{{ attributes[key] }}</th>
                    <th class="text-center">{{ value }}</th>
                    <th class="text-center">{{ getMax(key) }}</th>
                    <th class="text-center space-x-1">
                        <button class="btn btn-outline btn-primary btn-xs" id="{{ key }}IncreaseButton" @click="increase(key)">+</button>
                        <button class="btn btn-outline btn-accent btn-xs" id="{{ key }}DecreaseButton" @click="decrease(key)">-</button>
                    </th>
                </tr>
                <tr v-if="sheet.scripture != null">
                    <th>Pontos de escritura</th>
                    <th class="text-center">{{ sheet.scripture.remaining_scripture_points }}</th>
                    <th class="text-center">{{ sheet.maxAttributes["max_scripture_points"] }}</th>
                    <th class="text-center space-x-1">
                        <button class="btn btn-outline btn-primary btn-xs" id="{{ key }}IncreaseButton" @click="sheet.scripture.remaining_scripture_points++">+</button>
                        <button class="btn btn-outline btn-accent btn-xs" id="{{ key }}DecreaseButton" @click="sheet.scripture.remaining_scripture_points--">-</button>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
