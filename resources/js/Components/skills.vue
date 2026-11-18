<script setup lang="ts">
import { AxiosError } from "axios";
import { watch } from "vue";
import ToastError from "@scripts/ToastError.ts";
import SectionCard from "@ui/SectionCard.vue";
import Stepper from "@ui/Stepper.vue";
import PipMeter from "@ui/PipMeter.vue";
import HoverHint from "@ui/HoverHint.vue";
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

/** Skill explanations from the rulebook doc "Explicação de Habilidades" (the doc calls "knowledge" Informação). */
const skillDescriptions: AssociativeArray = {
    "speed": "Habilidade que dita a quantidade de pontos de movimento e situações que exigem velocidade, como correr mais rápido do que alguém.",
    "acrobatics": "Habilidade ligada à esquiva em combate, além de situações que exijam movimentos complicados, como executar um salto longo ou escalar algo.",
    "melee": "Habilidade usada em tudo que rege combate corpo a corpo, desde bater com um porrete como um neandertal até o uso de espadas e lanças.",
    "intimidation": "Habilidade usada no convencimento através da força. Exibir força previamente, como causar dano ao alvo pode diminuir a dificuldade da intimidação, mas o uso da intimidação causa o desgaste da relação entre os envolvidos e quebra de confiança.",
    "ranged": "Habilidade usada em tudo que rege combate à distância, desde o uso de armas de fogo e arcos até arremessos de objetos.",
    "stealth": "Habilidade usada em toda ação furtiva, desde ataques, furtar objetos ou até mesmo arrombar uma fechadura.",
    "conscience": "Habilidade usada em testes de percepção para tentar encontrar perigos escondidos, como armadilhas. Em geral, é usado para tentar prever coisas que podem acontecer.",
    "investigation": "Habilidade usada para investigar, buscar objetos escondidos, determinar tipos de ferimentos, determinar eventos que ocorreram. Em geral, é usado para tentar determinar coisas que já aconteceram.",
    "wisdom": "Habilidade usada quando o personagem necessita de um conhecimento técnico, como saber a fórmula de bhaskara ou invadir um terminal com senha.",
    "knowledge": "Habilidade usada para saber se um personagem tem uma informação sobre o mundo, sobre o local, sobre um organização, sobre uma pessoa e etc. Obviamente, informações que não são públicas não podem ser obtidas por rolagem de informação e devem ser obtidas de outras formas.",
    "medicine": "Habilidade usada quando o personagem tenta exercer um ato médico, como aplicar primeiros socorros, saber como lidar com determinado tipo de ferimento ou identificar determinada doença.",
    "survival": "Habilidade ligada a ações de caça, montagem de armadilhas, identificação de plantas, montagem de acampamento, encontrar água e etc. Em geral, é ligada à busca de recursos.",
    "tenacity": "Habilidade de se manter firme em situações difíceis, como se manter consciente após um ataque forte, se manter pendurado em um telhado. Em geral, é usada quando o personagem tem que manter uma condição cansativa.",
    "diplomacy": "Habilidade de conversar bem e ser convincente. Ajuda em diálogos para que sua palavra tenha mais força, que suas promessas pareçam mais verdadeiras, que as pessoas estejam mais dispostas a compartilhar informações. Em geral, quando o personagem fala, é usada essa habilidade.",
    "insight": "Habilidade de ver através de mentiras. Pode ser usada em diálogos para determinar se alguém fala a verdade ou não. Em geral, essa habilidade é usada quando o personagem analisa algo que lhe foi falado."
}

function increase(key: string | number) : void {
    props.sheet.skills[key]++;
}

function decrease(key: string | number) : void {
    props.sheet.skills[key]--;
}

function maxFor(key: string | number) : number {
    return 2 * props.sheet.stats[props.sheet.skillsRelations[key]];
}

function canIncrease(key: string | number, value: number) : boolean {
    return value < maxFor(key);
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
                <span class="min-w-0 flex-1 truncate text-sm font-medium">
                    <HoverHint v-if="skillDescriptions[key]" :text="skillDescriptions[key]">{{ skills[key] ?? key }}</HoverHint>
                    <template v-else>{{ skills[key] ?? key }}</template>
                </span>
                <PipMeter :value="skill" :max="maxFor(key)" />
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
