<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

const props = defineProps({
    sheet: Object,
    rolls : Object
})

const emit = defineEmits(["sync"]);

const types = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    null: "Outro"
}

async function rollSpell(school, spell) {
    let cost = window.prompt("Insira o custo");
    if (props.sheet.attributes.mana < cost) {
        alert("Mana insuficiente");
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
    <div class="overflow-x-auto border border-1 rounded-md border-primary p-3">
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
        <div class="w-full text-center">
            <button class="btn btn-outline btn-primary w-full" @click="this.$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
