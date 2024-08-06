<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

const props = defineProps({defaultValue: String, title: String});
const emit = defineEmits(["end"]);

const modalRef = ref(null);
const input = defineModel();

defineExpose({modalRef, input});

function confirm() {
    emit("end", input.value);
}

</script>

<template>
    <dialog class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <h1 class="text-center text-xl font-bold">{{ title }}</h1>
            <div class="h-5"></div>
            <form method="dialog" class="flex gap-5">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                <input v-model="input" type="text" class="input input-bordered input-primary w-full" id="textInput" />
                <button class="btn btn-outline btn-secondary" @click="confirm()">Confirmar</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
