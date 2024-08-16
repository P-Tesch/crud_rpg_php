<script setup lang="ts">
import { ref } from "vue"

interface Props {
    title: string;
}
const props = defineProps<Props>();
const emit = defineEmits(["end"]);

const modalRef = ref<HTMLDialogElement>();
const input = defineModel<string>();

defineExpose({modalRef, input});

function confirm() : void {
    emit("end", input.value);
}

</script>

<template>
    <dialog class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl h-1/2 outline outline-primary overflow-auto">
            <h1 class="text-center text-xl font-bold">{{ title }}</h1>
            <div class="h-5"></div>
            <form method="dialog" class="flex gap-5 h-5/6">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                <textarea v-model="input" type="text" class="textarea textarea-primary w-full max-h-full h-full" id="textAreaInput" />
                <button class="btn btn-outline btn-secondary self-end" @click="confirm()">Confirmar</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
