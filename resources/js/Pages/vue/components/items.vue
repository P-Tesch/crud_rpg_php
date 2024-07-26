<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

defineProps({ sheet: Object })

async function rollItem(item) {
    /// TODO formatar resposta
    const url = /*window.location.host + */"/api/roll/item?item=" + item.id;
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        const json = JSON.parse(await response.text());
        alert(JSON.stringify(json["rolls"]));
        window.location.reload();
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

</script>


<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary p-3">
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
