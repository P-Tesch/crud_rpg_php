<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from "vue"

const props = defineProps({ sheet: Object })
const rolls = ref([]);

const endOfHistory = ref(null);
watch(rolls, () => {scrollToBottom()});

async function setup() {
    await setupRolls();
    scrollToBottom();
}

setup();

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

defineExpose({rolls});

Echo.channel("rolls").listen(".rollsTableUpdated", (echo) => {
    rolls.value = echo;
});

function getUserStyle(roll) {
    return roll["id"] == props.sheet.id ? "chat chat-end w-full text-right " : "chat chat-start w-full text-left";
}

function getBubbleStyle(roll) {
    return roll["id"] == props.sheet.id ? "chat-bubble chat-bubble-primary float-right" : "chat-bubble chat-bubble-primary float-left";
}

async function setupRolls() {
    const url = "/api/roll";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        rolls.value = JSON.parse(await response.text());
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function scrollToBottom() {
    endOfHistory.value.scrollIntoView(false);
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
                    :src="roll['portrait']" />
                </div>
            </div>
            <div class="w-full" v-if="roll['type'] == 'skill'">
                <div class="chat-header">{{ roll["name"] }} rolou {{ skills[roll["subject"]] }}</div>
                <div :class="getBubbleStyle(roll)">
                <span v-for="rollResult in roll['rolls']['rolls']">{{ rollResult }}&nbsp;</span>
                <p class="chat-footer">Total: {{ roll["rolls"]["hits"] }} Acertos</p>
                </div>
            </div>
            <div class="w-full" v-if="roll['type'] == 'spell'">
                <div class="chat-header">{{ roll["name"] }} rolou {{ roll["subject"] }} com custo {{ roll["cost"] }}</div>
                <div :class="getBubbleStyle(roll)">
                    <p>Coice: </p>
                    <p>Dano Recebido: {{ roll["recoilDamage"] }}</p>
                    <span>Rolagem: </span>
                    <span v-for="rollResult in roll['rolls']['recoil']['rolls']">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll["rolls"]["recoil"]["hits"] }} Acertos</p>
                </div>
                <div class="h-1 clear-both" v-if="roll['rolls']['success'] != null"></div>
                <div :class="getBubbleStyle(roll)" v-if="roll['rolls']['success'] != null">
                    <span>Sucesso: </span>
                    <span v-for="rollResult in roll['rolls']['success']['rolls']">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll["rolls"]["success"]["hits"] }} Acertos</p>
                </div>
                <div class="h-1 clear-both" v-if="roll['rolls']['success'] != null"></div>
                <div :class="getBubbleStyle(roll)" v-if="roll['rolls']['specific'] != null">
                    <span>Específico: </span>
                    <span v-for="rollResult in roll['rolls']['specific']['rolls']">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll["rolls"]["specific"]["hits"] }} Acertos</p>
                </div>
            </div>
        </div>
        <div ref="endOfHistory"></div>
    </div>
</template>
