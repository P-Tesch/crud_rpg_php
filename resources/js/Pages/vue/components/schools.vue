<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"
import FailToast from './alerts/failToast.vue';

const props = defineProps({
    sheet: Object,
    rolls : Object
})

const emit = defineEmits(["sync"]);
const failToast = ref(null);

const types = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    null: "Outro"
}

async function rollSpell(school, spell) {
    let cost = window.prompt("Insira o custo");
    if (props.sheet.attributes.mana < cost) {
        this.failToast.toastRef = true;
        setTimeout(() => this.failToast.toastRef = false, 2500);
        return;
    }

    const url = /*window.location.host + */"/api/roll/spell?school=" + school + "&spell=" + spell + "&cost=" + cost + "&modifier=0";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        const json = JSON.parse(await response.text());
        props.rolls.rolls.push({"type": "spell", "json": json});
        emit("sync");

    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary p-3 flex flex-col justify-between">
        <div class="overflow-auto">
            <div v-for="value, key in sheet.schools" class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" name="schools-collapse" />
                <div class="collapse-title text-xl font-medium">{{ key }} [{{ value.level }}]</div>
                <div class="collapse-content">
                    <div v-for="v, k in value.spells" class="collapse collapse-arrow bg-base-100">
                        <input type="checkbox" name="spells-collapse" />
                        <div class="collapse-title text-xl font-medium">{{ k }}</div>
                        <div class="collapse-content">
                            <p>Tipo: {{ types[v.type] }}</p>
                            <p>Descrição: {{ v.description }}</p>
                            <button class="btn btn-outline btn-secondary btn-sm" @click="rollSpell(key, k)">Roll</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full" @click="this.$emit('add')">Adicionar</button>
        </div>
    </div>
    <FailToast class="z-10" ref="failToast" :message="'Mana insuficiente'" />
</template>
