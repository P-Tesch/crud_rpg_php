<script setup lang="ts">
import { ref, onBeforeMount } from "vue"
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, Miracle } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const modalRef = ref<HTMLDialogElement>();
const miracles = ref<Miracle[]>([]);

onBeforeMount(() => { getMiracles() })

defineExpose({ modalRef });

function getMiracles() : void {
    window.axios.get("/api/miracles")
        .then((response: AxiosResponse) =>{
            miracles.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao buscar milagres", error);
        }
    );
}

function addToSheet(toAdd: Miracle) : void {
    modalRef.value?.close();

    if (miracles.value == null) {
        throw new ToastError("A lista de milagres está vazia");
    }

    let original: Miracle[] = props.sheet.miracles;

    let shouldAdd: boolean = true;
    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                shouldAdd = false;
            }
        }
    );

    if (shouldAdd) {
        props.sheet.miracles.push(toAdd);
    }
}

function possibleMiracles() : Miracle[] {
    let possibleMiracles: Miracle[] = [];

    miracles.value.forEach(miracle => {
        let shouldAdd = props.sheet.miracles.every(sheetMiracle => {
            return sheetMiracle.name != miracle.name;
        });

        if (shouldAdd) {
            possibleMiracles.push(miracle);
        }
    });

    return possibleMiracles;
}

</script>

<template>
    <dialog id="miracles_modal" class="modal" ref="modalRef">
        <BaseModal title="Milagres" subtitle="Loja" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="miracle in possibleMiracles()"
                    :key="miracle.id"
                    :title="miracle.name"
                    :cost="miracle.cost"
                    @add="addToSheet(miracle)"
                >
                    <p class="leading-relaxed">{{ miracle.description }}</p>
                </ShopItemCard>
            </div>
            <p v-if="possibleMiracles().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
