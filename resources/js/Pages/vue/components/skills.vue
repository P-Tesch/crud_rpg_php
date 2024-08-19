<script setup lang="ts">
import { Sheet, Skills } from "rpgTypes";
import { watch } from "vue";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const originalSkills: Skills = Object.assign({}, props.sheet.skills);
watch(props.sheet.stats, () => {
    Object.entries(props.sheet.skillsRelations).forEach((keys) => {
        if (props.sheet.stats[keys[0]] > props.sheet.stats[keys[1]] * 2) {
            props.sheet.skills[keys[0]] = props.sheet.stats[keys[1]] * 2;
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

function increase(key: string | number) : void {
    props.sheet.skills[key]++;
}

function decrease(key: string | number) : void {
    props.sheet.skills[key]--;
}

function canIncrease(key: string | number, value: number) : boolean {
    return value <= 2 * props.sheet.stats[props.sheet.skillsRelations[key]] - 1;
}

function canDecrease(key: string | number, value: number) : boolean {
    return originalSkills[key] < value;
}

async function rollSkill(key: string | number) {
    const url: string = "/api/roll/skill?skill=" + key + "&modifier=0";

    window.axios.get(url)
        .catch(() => {
            throw new Error("Falha ao rolar habilidade");
        }
    );
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
                <tr v-for="skill, key in sheet.skills">
                    <th>{{ skills[key] }}</th>
                    <th>{{ skill }}</th>
                    <th class="space-x-1">
                        <button class="btn btn-outline btn-primary btn-xs" id="{{ key }}IncreaseButton"v-if="canIncrease(key, skill)" @click="increase(key)">+</button>
                        <button class="btn btn-outline btn-accent btn-xs" id="{{ key }}DecreaseButton" v-if="canDecrease(key, skill)" @click="decrease(key)">-</button>
                    </th>
                    <th>
                        <button class="btn btn-outline btn-secondary btn-sm" id="{{ key }}RollButton" @click="rollSkill(key)">Rolar</button>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
