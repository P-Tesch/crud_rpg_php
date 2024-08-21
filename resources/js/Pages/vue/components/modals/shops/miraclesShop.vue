<script setup lang="ts">
import { ref, onBeforeMount } from "vue"
import type { Sheet, Miracle } from "rpgTypes";
import { AxiosError, AxiosResponse } from "axios";
import ToastError from "../../../../../ToastError";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const modalRef = ref<HTMLDialogElement>();
const miracles = ref<Miracle[]>([]);

onBeforeMount(() => { getMiracles() })

defineExpose({ modalRef });

function getMiracles() : void {
    const url: string = "/api/miracles";

    window.axios.get(url)
        .then((response: AxiosResponse) =>{
            miracles.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao buscar milagres", error);
        }
    );
}

function addToSheet(index: number) : void {
    if (miracles.value == null) {
        throw new ToastError("A lista de milagres está vazia");
    }

    let toAdd: Miracle = miracles.value[index];
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

</script>

<template>
    <dialog id="miracles_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Milagres</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="miracle, key in miracles">
                    <p class="text-2xl">{{ miracle.name }}</p>
                    <p class="text-md"> {{ miracle.description }}</p>
                    <p class="text-md">Custo: {{ miracle.cost }}</p>
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
