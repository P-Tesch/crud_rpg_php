<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, Advantage } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const modalRef = ref<HTMLDialogElement>();
const advantages = ref<Advantage[]>([]);

onBeforeMount(() => { getAdvantages() });

defineExpose({ modalRef });

function getAdvantages() : void {
    window.axios.get("/api/advantages")
        .then((response : AxiosResponse) => {
            advantages.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao buscar vantagens", error);
        }
    );
}

function addToSheet(index: number) : void {
    if (advantages.value == null) {
        throw new ToastError("A lista de vantagens está vazia");
    }

    let toAdd: Advantage = advantages.value[index];
    let original: Advantage[] = props.sheet.advantages;
    let shouldAdd: boolean = true;

    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                if (value.level >= toAdd.level) {
                    shouldAdd = false;
                } else {
                    props.sheet.advantages.splice(props.sheet.advantages.indexOf(value), 1);
                }
            }
        }
    );

    if (shouldAdd) {
        props.sheet.advantages.push(toAdd);
    }
}

</script>

<template>
    <dialog id="eyes_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Vantagens</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="advantage, key in advantages">
                    <p class="text-2xl">{{ advantage.name }}</p>
                    <p class="text-md"> {{ advantage.description }}</p>
                    <p class="text-md">Level: {{ advantage.level }}</p>
                    <p class="text-md">Custo: {{ advantage.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end"
                        @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
