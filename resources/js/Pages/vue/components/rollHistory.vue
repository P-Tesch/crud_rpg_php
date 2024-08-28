<script setup lang="ts">
import { AxiosError } from "axios";
import { ref, watch, onMounted } from "vue";
import ToastError from "@scripts/ToastError.ts";
import type { RollAssociative, Sheet, AssociativeArray } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const rolls = ref<RollAssociative[]>([]);

const endOfHistory = ref<HTMLDivElement>();
watch(rolls, () => { scrollToBottom() });

onMounted(() => {
    setupRolls();
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

defineExpose({rolls});

window.Echo.channel("rolls").listen(".rollsTableUpdated", (echo: RollAssociative[]) => {
    rolls.value = echo;
});

function getUserStyle(roll: RollAssociative) : string {
    return roll["id"] == props.sheet.id ? "chat chat-end w-full text-right " : "chat chat-start w-full text-left";
}

function getBubbleStyle(roll: RollAssociative) : string {
    return roll["id"] == props.sheet.id ? "chat-bubble chat-bubble-primary float-right" : "chat-bubble chat-bubble-primary float-left";
}

function setupRolls() : void {
    const url: string = "/api/roll";

    window.axios.get(url)
        .then((response: AxiosResponse) => {
            rolls.value = response.data;
            setTimeout(() => {scrollToBottom()}, 1);
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar histórico de rolagens", error);
        }
    );
}

function scrollToBottom() : void {
    endOfHistory?.value?.scrollIntoView(false);
}

</script>

<template>
    <div id="rollHistoryChat" class="border border-1 rounded-md border-primary px-3 overflow-auto max-h-72 w-full">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3 sticky top-0 bg-base-100 z-10">
            <h1 class="font-semibold text-2xl">Rolagens</h1>
        </div>

        <div :class="getUserStyle(roll)" v-for="roll in rolls" ref="history">
            <div class="chat-image avatar w-fit">
                <div class="w-10 rounded-full">
                    <img
                    :src="roll.portrait" />
                </div>
            </div>
            <div class="w-full" v-if="roll.type == 'skill'">
                <div class="chat-header">{{ roll.name }} rolou {{ skills[roll.subject] }}</div>
                <div :class="getBubbleStyle(roll)">
                <span v-for="rollResult in roll.rolls.skill?.rolls">{{ rollResult }}&nbsp;</span>
                <p class="chat-footer">Total: {{ roll.rolls.skill?.hits }} Acertos</p>
                </div>
            </div>
            <div class="w-full" v-if="roll.type == 'spell'">
                <div class="chat-header">{{ roll.name }} rolou {{ roll.subject }} com custo {{ roll.cost }}</div>
                <div :class="getBubbleStyle(roll)">
                    <p>Coice: </p>
                    <p>Dano Recebido: {{ roll.recoilDamage }}</p>
                    <span>Rolagem: </span>
                    <span v-for="rollResult in roll.rolls.recoil?.rolls">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll.rolls.recoil?.hits }} Acertos</p>
                </div>
                <div class="h-1 clear-both" v-if="roll.rolls.success != null"></div>
                <div :class="getBubbleStyle(roll)" v-if="roll.rolls.success != null">
                    <span>Sucesso: </span>
                    <span v-for="rollResult in roll.rolls.success.rolls">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll.rolls.success.hits }} Acertos</p>
                </div>
                <div class="h-1 clear-both" v-if="roll.rolls.success != null"></div>
                <div :class="getBubbleStyle(roll)" v-if="roll.rolls.specific != null">
                    <span>Específico: </span>
                    <span v-for="rollResult in roll.rolls.specific.rolls">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll.rolls.specific.hits }} Acertos</p>
                </div>
            </div>
            <div class="w-full" v-if="roll.type == 'mystic_eye'">
                <div class="chat-header">{{ roll.name }} rolou {{ roll.subject }} alvejando {{ roll.target }}</div>
                <div :class="getBubbleStyle(roll)">
                <span v-for="rollResult in roll.rolls.mystic_eye?.rolls">{{ rollResult }}&nbsp;</span>
                <p class="chat-footer">Total: {{ roll.rolls.mystic_eye?.hits }} Acertos</p>
                </div>
            </div>
        </div>
        <div ref="endOfHistory"></div>
    </div>
</template>
