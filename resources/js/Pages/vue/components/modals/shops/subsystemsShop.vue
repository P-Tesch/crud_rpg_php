<script setup lang="ts">
import { ref } from "vue";
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
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Subsistemas</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="subsystem in possibleSubsystems()">
                    <h4 class="text-xl font-semibold">{{ subsystem.name }}</h4>
                    <p>{{ subsystem.description }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(subsystem)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
