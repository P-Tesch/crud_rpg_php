<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, onBeforeMount } from "vue"

const props = defineProps({
    sheet: Object
})
const modalRef = ref(null);
const miracles = ref(null);

onBeforeMount(() => { getMiracles() })

defineExpose({ modalRef });

async function getMiracles() {
    const url = "/api/miracles";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        miracles.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function addToSheet(index) {
    let toAdd = miracles.value[index];
    let original = props.sheet.miracles;
    let added = false;
    let newMiracles = [];
    original.forEach(miracle => {
        if (miracle.name == toAdd.name) {
            if (toAdd.level <= miracle.level) {
                newMiracles.push(miracle);
            }
            else {
                newMiracles.push(toAdd);
            }
            added = true;
        }
        else {
            newMiracles.push(miracle);
        }
    });

    if (!added) {
        newMiracles.push(toAdd);
    }

    props.sheet.miracles = newMiracles;
}

</script>

<template>
    <dialog id="miracles_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Milagres</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="value, key in miracles">
                    <p class="text-2xl">{{ value.name }}</p>
                    <p class="text-md"> {{ value.description }}</p>
                    <p class="text-md">Custo: {{ value.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end"
                        @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
    </dialog>
</template>
