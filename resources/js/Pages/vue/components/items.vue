<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

defineProps({ sheet: Object });
const emit = defineEmits(["sync"]);

async function rollItem(item) {
    const url = "/api/roll/item?item=" + item.id;
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        const json = JSON.parse(await response.text());
        alert(JSON.stringify(json["rolls"]));
        emit("sync");
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

</script>


<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Inventário</h1>
        </div>

        <div v-for="value, key in sheet.items" class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="items-collapse" />
            <div class="collapse-title text-xl font-medium">{{ value.name }}</div>
            <div class="collapse-content grid grid-flow-row grid-cols-2">
                <p class="col-start-1 row-start-1" >{{ value.description }}</p>
                <p class="col-start-1 row-start-2" v-if="value.damage != null">Damage: {{ value.damage }}</p>
                <button class="btn btn-outline btn-secondary btn-sm grid-rows-1 col-start-2 row-span-full w-4/12 ml-auto my-auto mr-5" @click="rollItem(value)">Roll</button>
            </div>
        </div>
    </div>
</template>
