<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, onBeforeMount } from "vue"

const props = defineProps({
    sheet: Object
})
const modalRef = ref(null);
const scriptureAbilities = ref(null);

onBeforeMount(() => { getScriptureAbilities() })

defineExpose({ modalRef });

async function getScriptureAbilities() {
    const url = "/api/scripture_abilities";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        scriptureAbilities.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function addToSheet(index) {
    let toAdd = scriptureAbilities.value[index];
    let original = props.sheet.scripture.scriptureAbilities;

    let shouldAdd = true;
    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                if (value.level >= toAdd.level) {
                    shouldAdd = false;
                } else {
                    props.sheet.scripture.scriptureAbilities.splice(props.sheet.scripture.scriptureAbilities.indexOf(value), 1);
                }
            }
        }
    );

    if (shouldAdd) {
        props.sheet.scripture.scriptureAbilities.push(toAdd);
    }
}

</script>

<template>
    <dialog id="scripture_abilities_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Habilidades de escritura</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="value, key in scriptureAbilities">
                    <p class="text-2xl">{{ value.name }}</p>
                    <p class="text-md"> {{ value.description }}</p>
                    <p class="text-md">Level: {{ value.level }}</p>
                    <p class="text-md">Custo: {{ value.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end"
                        @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
