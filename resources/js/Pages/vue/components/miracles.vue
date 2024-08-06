<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

defineProps({ sheet: Object });

const emit = defineEmits("add");

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Milagres</h1>
        </div>

        <div v-for="value, key in sheet.miracles" class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="miracles-collapse" />
            <div class="collapse-title text-xl font-medium">{{ value.name }}</div>
            <div class="collapse-content">
                <p>{{ value.description }}</p>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="this.$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
