<script setup lang="ts">
import { ref } from "vue"
import BaseModal from "@ui/BaseModal.vue";

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
        <BaseModal :title="title" size="sm">
            <form method="dialog" class="flex flex-col gap-3" @submit="confirm()">
                <input v-model="input" type="text" class="input w-full" id="textInput" autofocus />
                <div class="flex justify-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">Confirmar</button>
                </div>
            </form>
        </BaseModal>
    </dialog>
</template>
