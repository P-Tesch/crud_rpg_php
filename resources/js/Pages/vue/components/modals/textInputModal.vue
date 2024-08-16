<script setup lang="ts">
import { ref } from "vue"

interface Props {
    title: string;
}
const props = defineProps<Props>();
const emit = defineEmits(["end"]);

const modalRef = ref<HTMLDialogElement>();
const input = defineModel<HTMLInputElement, string>();

defineExpose({modalRef, input});

function confirm() : void {
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
