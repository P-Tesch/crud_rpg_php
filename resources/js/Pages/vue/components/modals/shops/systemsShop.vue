<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { AxiosResponse } from "axios";
import type { Sheet, SystemFromShop } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const systems = ref<SystemFromShop[]>([]);

onBeforeMount(() => {getSystems()})

defineExpose({modalRef});

function getSystems() : void {
    const url: string = "/api/systems";

    window.axios.get(url)
        .then((response: AxiosResponse) => {
            systems.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar sistemas", error);
        }
    );
}

function addToSheet(index: number) : void {
    if (props.sheet.systems.length != undefined) {
        props.sheet.systems = {};
    }

    const maxSystems = props.sheet.stats["tech"];

    let usedSlots = 0;
    Object.values(props.sheet.systems).forEach(system => {
        usedSlots += system.subsystems.length + 1;
    });

    if (usedSlots >= maxSystems) {
        throw new ToastError("O personagem já possui o número máximo de sistemas e subsistemas");
    }

    const system = systems.value[index];

    if (props.sheet.systems.hasOwnProperty(system.name)) {
        throw new ToastError("O personagem já possui esse sistema");
    }

    props.sheet.systems[system.name] = {
        id: system.id,
        subsystems: []
    };
}

</script>

<template>
    <dialog id="systems_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Sistemas</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="system, key in systems">
                    <h4 class="text-xl font-semibold">{{ system.name }}</h4>
                    <p>{{ system.description }}</p>
                    <div v-for="subsystem in system.subsystems" class="collapse collapse-arrow bg-base-100">
                        <input type="checkbox" name="subsystems-collapse" />
                        <div class="collapse-title text-xl font-medium">{{ subsystem.name }}</div>
                        <div class="collapse-content">
                            <p>Descrição: {{ subsystem.description }}</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
