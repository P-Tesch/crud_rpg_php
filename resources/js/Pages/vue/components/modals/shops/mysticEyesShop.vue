<script setup lang="ts">
import { ref, onBeforeMount } from "vue"
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
        const url: string = "/api/mystic_eyes";

        window.axios.get(url)
            .then((response: AxiosResponse) => {
                eyes.value = response.data["data"];
            }
        ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao buscar olhos místicos", error);
        }
    );
}

function addToSheet(index: number) : void {
    if (eyes.value == null) {
        throw new ToastError("A lista de olhos místicos está vazia");
    }

    let toAdd: MysticEye = eyes.value[index];
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

</script>

<template>
    <dialog id="eyes_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Olhos místicos</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="eye, key in eyes">
                    <div class="collapse collapse-arrow bg-base-100">
                        <input type="checkbox" name="mystic-eyes-collapse" />
                        <div class="collapse-title text-xl font-medium">{{ eye.name }}</div>
                        <div class="collapse-content">
                            <div v-if="eye.passive != null" class="collapse collapse-arrow bg-base-100">
                                <input type="checkbox" name="me-passive-collapse" />
                                <div class="collapse-title text-xl font-medium">Passivo</div>
                                <div class="collapse-content">
                                    <p>{{ eye.passive }}</p>
                                </div>
                            </div>
                            <div v-if="eye.active != null" class="collapse collapse-arrow bg-base-100">
                                <input type="checkbox" name="me-passive-collapse" />
                                <div class="collapse-title text-xl font-medium">Ativo</div>
                                <div class="collapse-content grid grid-flow-row grid-cols-2">
                                    <p class="col-start-1 row-start-1">{{ eye.active }}</p>
                                    <p class="col-start-1 row-start-2">Cooldown: {{ eye.cooldown }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="p-5">Custo: {{ eye.cost }}</p>
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
