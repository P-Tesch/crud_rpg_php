<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, onBeforeMount } from "vue"

const props = defineProps({
    sheet: Object
})
const modalRef = ref(null);
const advantages = ref(null);

onBeforeMount(() => { getAdvantages() })

defineExpose({ modalRef });

async function getAdvantages() {
    const url = "/api/advantages";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        advantages.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function addToSheet(index) {
    let toAdd = advantages.value[index];
    let original = props.sheet.advantages;

    let shouldAdd = true;
    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                if (value.level >= toAdd.level) {
                    shouldAdd = false;
                } else {
                    props.sheet.advantages.splice(props.sheet.advantages.indexOf(value), 1);
                }
            }
        }
    );

    if (shouldAdd) {
        props.sheet.advantages.push(toAdd);
    }
}

</script>

<template>
    <dialog id="eyes_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Vantagens</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="value, key in advantages">
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
