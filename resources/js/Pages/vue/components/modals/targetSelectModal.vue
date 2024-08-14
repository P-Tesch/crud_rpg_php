<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, onBeforeMount, onMounted } from "vue"

const props = defineProps({ sheet: Object, csrf: String });
const emit = defineEmits(["end"]);

const modalRef = ref(null);
const characters = ref([]);

const updateCharacters = async () => {
    const response = await fetch("/api/live", {
        method: "GET"
    });

    characters.value = JSON.parse(await response.text());
}

defineExpose({modalRef, updateCharacters});

const heartbeat = async () => {
    fetch("/api/live",{
        method: "POST",
        headers: {
            "X-CSRF-Token": props.csrf,
            "Content-Type": "application/json"
        },
        credentials: "same-origin",
        body:
        JSON.stringify({
            sheet_id: props.sheet.id,
            name: props.sheet.name,
            portrait: props.sheet.portrait,
            timestamp: new Date().getTime()
        })
    })
};

heartbeat();

setInterval(heartbeat, 10000);

function select(key) {
    emit("end", key);
}

</script>

<template>
    <dialog class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <h1 class="text-center text-xl font-bold">Alvos</h1>
            <div class="h-5"></div>
            <div class="flex flex-col gap-5" >
                <div v-for="character, key in characters" class="outline outline-primary rounded-2xl flex p-5">
                    <img :src="character.portrait" class=" rounded-badge outline outline-primary max-w-20 max-h-20 left-0 right-auto"/>
                    <label class=" text-3xl font-semibold my-auto p-5">{{ character.name }}</label>
                    <button class="btn btn-outline btn-secondary my-auto ml-auto mr-0" @click="select(key)">Selecionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
