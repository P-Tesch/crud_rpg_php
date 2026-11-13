<script setup lang="ts">
import { AxiosError } from "axios";
import { watch } from "vue";
import ToastError from "@scripts/ToastError.ts";
import SectionCard from "@ui/SectionCard.vue";
import Stepper from "@ui/Stepper.vue";
import RollButton from "@ui/RollButton.vue";
import type { AssociativeArray, Sheet, Skills } from "rpgTypes";

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

const skills: AssociativeArray = {
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
    window.axios.get("/api/roll/skill", {
        params: {
            skill: key,
            modifier: 0
        }
    }).catch((error: AxiosError) => {
            throw new ToastError("Falha ao rolar habilidade");
        }
    );
}

</script>

<template>
    <SectionCard title="Habilidades">
        <ul class="flex flex-col">
            <li v-for="skill, key in sheet.skills" :key="key" class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                <span class="min-w-0 flex-1 truncate text-sm font-medium">{{ skills[key] ?? key }}</span>
                <span class="w-7 text-right font-mono text-lg font-semibold tabular-nums">{{ skill }}</span>
                <Stepper
                    :label="skills[key]"
                    :can-increase="canIncrease(key, skill)"
                    :can-decrease="canDecrease(key, skill)"
                    @increase="increase(key)"
                    @decrease="decrease(key)"
                />
                <RollButton @click="rollSkill(key)" />
            </li>
        </ul>
    </SectionCard>
</template>
