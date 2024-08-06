<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

const props = defineProps({ sheet: Object });

const originalAdvantages = Object.assign({}, props.sheet.advantages);

const emit = defineEmits(["add"]);

function isOriginal(value) {
    let advantagesArray = Object.values(originalAdvantages);
    let isOriginal = false;
    advantagesArray.forEach(
        (v) => {
            if (v.name == value.name) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Vantagens</h1>
        </div>

        <div v-for="value, key in sheet.advantages" class="collapse collapse-arrow bg-base-100">
            <button v-if="isOriginal(value)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="sheet.advantages.splice(key, 1)">✕</button>
            <input type="checkbox" name="advantages-collapse" />
            <div class="collapse-title text-xl font-medium">{{ value.name }}</div>
            <div class="collapse-content">
                <p>{{ value.description }}</p>
                <p>Level: {{ value.level }}</p>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
