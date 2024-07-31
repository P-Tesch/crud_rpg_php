<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from "vue"

defineProps({ sheet: Object })
const rolls = ref([]);

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

</script>

<template>
    <div id="rollHistoryChat" class="border border-1 rounded-md border-primary px-3 overflow-auto max-h-72 w-full">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Rolagens</h1>
        </div>

        <div class="chat chat-start w-full" v-for="roll in rolls">
            <div class="chat-image avatar">
                <div class="w-10 rounded-full">
                    <img
                    :src="sheet.portrait" />
                </div>
            </div>
            <div class="w-full" v-if="roll.type == 'skill'">
                <div class="chat-header">{{ roll["json"]["name"] }} rolou {{ skills[roll["json"]["subject"]] }}</div>
                <div class="chat-bubble chat-bubble-primary">
                <span v-for="rollResult in roll['json']['rolls']['rolls']">{{ rollResult }}&nbsp;</span>
                <p class="chat-footer">Total: {{ roll["json"]["rolls"]["hits"] }} Acertos</p>
                </div>
            </div>
            <div class="w-full" v-if="roll.type == 'spell'">
                <div class="chat-header">{{ roll["json"]["name"] }} rolou {{ roll["json"]["subject"] }} com custo {{ roll["json"]["cost"] }}</div>
                <div class="chat-bubble chat-bubble-primary">
                    <p>Coice: </p>
                    <p>Dano Recebido: {{ roll["json"]["recoilDamage"] }}</p>
                    <span>Rolagem: </span>
                    <span v-for="rollResult in roll['json']['rolls']['recoil']['rolls']">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll["json"]["rolls"]["recoil"]["hits"] }} Acertos</p>
                </div>
                <div class="h-1" v-if="roll['json']['rolls']['success'] != null"></div>
                <div class="chat-bubble chat-bubble-primary" v-if="roll['json']['rolls']['success'] != null">
                    <span>Sucesso: </span>
                    <span v-for="rollResult in roll['json']['rolls']['success']['rolls']">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll["json"]["rolls"]["success"]["hits"] }} Acertos</p>
                </div>
                <div class="h-1" v-if="roll['json']['rolls']['success'] != null"></div>
                <div class="chat-bubble chat-bubble-primary" v-if="roll['json']['rolls']['specific'] != null">
                    <span>Específico: </span>
                    <span v-for="rollResult in roll['json']['rolls']['specific']['rolls']">{{ rollResult }}&nbsp;</span>
                    <p class="chat-footer">Total: {{ roll["json"]["rolls"]["specific"]["hits"] }} Acertos</p>
                </div>
            </div>
        </div>
    </div>
</template>
