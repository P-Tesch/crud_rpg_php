<script setup lang="ts">
import { ref, onBeforeMount } from "vue"
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, MysticEye } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const eyes = ref<MysticEye[]>([]);

onBeforeMount(() => { getEyes() })

defineExpose({ modalRef });

function getEyes() : void {
        window.axios.get("/api/mystic_eyes")
            .then((response: AxiosResponse) => {
                eyes.value = response.data["data"];
            }
        ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao buscar olhos místicos", error);
        }
    );
}

function addToSheet(toAdd: MysticEye) : void {
    modalRef.value?.close();

    if (eyes.value == null) {
        throw new ToastError("A lista de olhos místicos está vazia");
    }

    let original: MysticEye[] = props.sheet.mysticEyes;
    if (original.length >= 2) {
        throw new ToastError("O personagem ja possui o máximo de olhos místicos");
    }

    let exists: boolean = false;
    original.forEach(eye => {
        if (eye.name == toAdd.name) {
            exists = true;
        }
    });

    if (exists) {
        throw new ToastError("O personagem ja possui esse olho");
    }

    toAdd.pivot = {
        "current_cooldown": toAdd.cooldown,
        "sheet_id": props.sheet.id,
        "mystic_eye_id": toAdd.id
    };

    props.sheet.mysticEyes.push(toAdd);
}

function getPossibleEyes() : MysticEye[] {
    return eyes.value.filter(eye => {
        for (let i = 0; i < props.sheet.mysticEyes.length; i++) {
            return props.sheet.mysticEyes[i].name != eye.name;
        }
        return true;
    });
}

</script>

<template>
    <dialog id="eyes_modal" class="modal" ref="modalRef">
        <BaseModal title="Olhos místicos" subtitle="Loja · máximo de 2" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="eye in getPossibleEyes()"
                    :key="eye.id"
                    :title="eye.name"
                    :cost="eye.cost"
                    @add="addToSheet(eye)"
                >
                    <div v-if="eye.passive != null">
                        <p class="label-mono mb-1">Passivo</p>
                        <p class="leading-relaxed">{{ eye.passive }}</p>
                    </div>
                    <div v-if="eye.active != null">
                        <p class="label-mono mb-1">Ativo · recarga {{ eye.cooldown }}</p>
                        <p class="leading-relaxed">{{ eye.active }}</p>
                    </div>
                </ShopItemCard>
            </div>
            <p v-if="getPossibleEyes().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
