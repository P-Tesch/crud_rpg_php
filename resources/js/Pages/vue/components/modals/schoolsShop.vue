<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, onBeforeMount } from "vue"

const props = defineProps({
    sheet: Object
})
const modalRef = ref(null);
const schools = ref(null);

const types = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    null: "Outro"
}

onBeforeMount(() => {schools.value = getSchools()})

defineExpose({modalRef});

async function getSchools() {
    const url = /*window.location.host + */"/api/schools";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        schools.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function addToSheet(index) {
    let toAdd = schools.value[index];
    let original = props.sheet.schools[toAdd["name"]];
    let spells = {};
    toAdd["spells"].forEach(spell => {
        spells[spell["name"]] = {"type": spell["type"], "description": spell["description"]}
    });
    if (original != null) {
        original.level = toAdd["level"];
        original.spells = spells;
    }
    else {
        props.sheet.schools[toAdd["name"]] = {"level": toAdd["level"], "spells": spells};
    }
}

</script>

<template>
    <dialog id="schools_modal" class="modal" ref="modalRef">
    <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
        <form method="dialog">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-3xl font-bold text-center">Escolas</h3>
        <div class="flex flex-col gap-5">
            <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="value, key in schools">
                <h4 class="text-xl font-semibold">{{ value["name"] }}</h4>
                <p>{{ value["description"] }}</p>
                <p>Level: {{ value["level"] }}</p>
                <div v-for="v, k in value['spells']" class="collapse collapse-arrow bg-base-100">
                    <input type="checkbox" name="spells-collapse" />
                    <div class="collapse-title text-xl font-medium">{{ v["name"] }}</div>
                    <div class="collapse-content">
                        <p>Tipo: {{ types[v.type] }}</p>
                        <p>Descrição: {{ v.description }}</p>
                    </div>
                </div>
                <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(key)">Adicionar</button>
            </div>
        </div>
    </div>
    </dialog>
</template>
