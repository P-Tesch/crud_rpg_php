<script setup lang="ts">
import { ref } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import type { Sheet, Character } from "rpgTypes";
import ToastError from "@scripts/ToastError.ts";
import type { AxiosError, AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const emit = defineEmits(["end"]);

const modalRef = ref<HTMLDialogElement>();
const characters = ref<Character[]>([]);

const updateCharacters = async () => {
    window.axios.get("/api/live")
        .then((response: AxiosResponse) => {
            characters.value = response.data;
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao atualizar lista de personagens");
    });
}

defineExpose({modalRef, updateCharacters});

const heartbeat = (): void => {
    window.axios.post("/api/live", {
        sheet_id: props.sheet.id,
        name: props.sheet.name,
        portrait: props.sheet.portrait,
        timestamp: new Date().getTime()
    }).catch(() => {
        throw new ToastError("Falha ao sincronizar com o servidor");
    });
};

heartbeat();

setInterval(heartbeat, 60000);

function select(key: number) {
    emit("end", key);
    modalRef.value?.close();
}

</script>

<template>
    <dialog class="modal" ref="modalRef">
        <BaseModal title="Selecionar alvo" subtitle="Personagens online" size="sm">
            <div class="flex flex-col gap-2">
                <button
                    v-for="character, key in characters"
                    :key="character.sheet_id"
                    type="button"
                    class="group flex items-center gap-3 rounded-box border border-base-content/10 bg-base-100/60 p-2.5 text-left transition-colors hover:border-primary/50 hover:bg-primary/5"
                    @click="select(key)"
                >
                    <span class="size-11 shrink-0 overflow-hidden rounded-full bg-base-300 ring-1 ring-base-content/10">
                        <img v-if="character.portrait" :src="character.portrait" alt="" class="size-full object-cover" />
                    </span>
                    <span class="min-w-0 flex-1 truncate font-display font-semibold">{{ character.name }}</span>
                    <span class="btn btn-xs btn-soft btn-primary pointer-events-none">Selecionar</span>
                </button>
                <p v-if="characters.length === 0" class="py-8 text-center text-sm text-base-content/45">Nenhum personagem online.</p>
            </div>
        </BaseModal>
    </dialog>
</template>
