<script setup>
import { Head, router } from '@inertiajs/vue3'
import { toRef, ref, reactive, watch } from "vue"

const props = defineProps({ sheet: Object });

const attributes = {
    "health": "Vida",
    "initiative": "Iniciativa",
    "movement": "Movimento",
    "mana": "Mana"
}

function increase(sheet, key) {
  sheet.attributes[key]++;
}

function decrease(sheet, key) {
  sheet.attributes[key]--;
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary p-3">
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
                    <th class="text-center">{{ sheet.maxAttributes["max_" + key] }}</th>
                    <th class="text-center space-x-1">
                        <button class="btn btn-outline btn-primary btn-xs" id="{{ key }}IncreaseButton" @click="increase(sheet, key)">+</button>
                        <button class="btn btn-outline btn-accent btn-xs" id="{{ key }}DecreaseButton" @click="decrease(sheet, key)">-</button>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</template>
