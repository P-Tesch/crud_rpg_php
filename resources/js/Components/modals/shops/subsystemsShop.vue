<script setup lang="ts">
import { ref } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, Subsystem } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const subsystems = ref<Subsystem[]>([]);
const systemNameRef = ref<string>("");

defineExpose({modalRef, build});

function build(systemName: string) {
    window.axios.get("/api/subsystems", {
        params: {
            system_name: systemName
        }
    })
        .then((response: AxiosResponse) => {
            systemNameRef.value = systemName;
            subsystems.value = response.data["data"];
            modalRef.value?.showModal();
        }
    ).catch((error: AxiosError) => {
        window.document.body.innerHTML = JSON.stringify(error.response?.data)
        throw new ToastError("Falha ao buscar subsistemas", error);
    }
);
}

function addToSheet(subsystem: Subsystem) : void {
    modalRef.value?.close();

    props.sheet.systems[systemNameRef.value].subsystems.forEach((entry) => {
        if (entry.name == subsystem.name) {
            throw new ToastError("O personagem já possui esse subsistema");
        }
    });

    props.sheet.systems[systemNameRef.value].subsystems.push(subsystem);
}

function possibleSubsystems() : Subsystem[] {
    let possibleSubsystems: Subsystem[] = [];

    subsystems.value.forEach(subsystem => {
        let shouldAdd: boolean = true;

        props.sheet.systems[systemNameRef.value].subsystems.forEach(sheetSubsystem => {
            if (sheetSubsystem.name == subsystem.name) {
                shouldAdd = false;
            }
        });

        if (shouldAdd) {
            possibleSubsystems.push(subsystem);
        }
    });

    return possibleSubsystems;
}

</script>

<template>
    <dialog id="subsystem_modal" class="modal" ref="modalRef">
        <BaseModal title="Subsistemas" :subtitle="systemNameRef" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="subsystem in possibleSubsystems()"
                    :key="subsystem.name"
                    :title="subsystem.name"
                    @add="addToSheet(subsystem)"
                >
                    <p class="leading-relaxed">{{ subsystem.description }}</p>
                </ShopItemCard>
            </div>
            <p v-if="possibleSubsystems().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
