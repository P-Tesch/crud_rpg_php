<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from "vue"

const props = defineProps({
    sheet: Object,
    rolls: Object
});
const originalSkills = Object.assign({}, props.sheet.skills);
watch(props.sheet.stats, () => {
    Object.entries(props.sheet.skillsRelations).forEach((keys) => {
        let skills = new Map(Object.entries(props.sheet.skills));
        let stats = new Map(Object.entries(props.sheet.stats));
        if (skills.get(keys[0]) > stats.get(keys[1]) * 2) {
            props.sheet.skills[keys[0]] = stats.get(keys[1]) * 2;
        }
    })
});

const skills = {
    "speed": "Velocidade",
    "acrobatics": "Acrobacia",
    "melee": "Combate corpo a corpo",
    "intimidation": "Intimidação",
    "ranged": "Combate à distância",
    "stealth": "Furtividade",
    "conscience": "Consciência",
    "investigation": "Investigação",
    "wisdom": "Sabedoria",
    "knowledge": "Conhecimento",
    "medicine": "Medicina",
    "survival": "Sobrevivência",
    "tenacity": "Tenacidade",
    "diplomacy": "Diplomacia",
    "insight": "Perspicácia"
}

function increase(sheet, key) {
    sheet.skills[key]++;
}

function decrease(sheet, key) {
    sheet.skills[key]--;
}

function canIncrease(key, value) {
    return value <= 2 * props.sheet.stats[props.sheet.skillsRelations[key]] - 1;
}

function canDecrease(key, value) {
    return originalSkills[key] < value;
}

async function rollSkill(sheet, key) {
    const url = "/api/roll/skill?skill=" + key + "&modifier=0";
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(await response.text());
      }

      const json = JSON.parse(await response.text());
      props.rolls.rolls.push({"type": "skill", "json": json});
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Habilidades</h1>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Habilidade</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="value, key in sheet.skills">
                    <th>{{ skills[key] }}</th>
                    <th>{{ value }}</th>
                    <th class="space-x-1">
                        <button class="btn btn-outline btn-primary btn-xs" id="{{ key }}IncreaseButton"v-if="canIncrease(key, value)" @click="increase(sheet, key)">+</button>
                        <button class="btn btn-outline btn-accent btn-xs" id="{{ key }}DecreaseButton" v-if="canDecrease(key, value)" @click="decrease(sheet, key)">-</button>
                    </th>
                    <th>
                        <button class="btn btn-outline btn-secondary btn-sm" id="{{ key }}RollButton" @click="rollSkill(sheet, key)">Roll</button>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
