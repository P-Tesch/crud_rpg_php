<script setup lang="ts">
import { AxiosError } from "axios";
import { ref } from "vue";
import ToastError from "../../../ToastError";
import type { MysticEye, Sheet } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalMysticEyes: MysticEye[] = Object.assign({}, props.sheet.mysticEyes);
const selectedEye = ref<number>();

const rollMysticEye = (target: number) : void => {
    const url: string = "/api/roll/mystic_eyes?eye=" + selectedEye.value + "&target=" + target + "&modifier=0";

    window.axios.get(url)
        .catch((error: AxiosError) => {
            throw new ToastError("Falha ao rolar habilidade de olhos místicos", error);
        }
    );
};

defineExpose({rollMysticEye});

const emit = defineEmits(["add", "target"]);

function selectTarget(eyeId: number) : void {
    selectedEye.value = eyeId;
    emit("target");
}

function isOriginal(eye: MysticEye) : boolean {
    let originalArray = Object.values(originalMysticEyes);
    let isOriginal: boolean = false;
    originalArray.forEach(
        (original) => {
            if (original.name == eye.name) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

</script>

<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Olhos místicos</h1>
        </div>

        <div v-for="eye, key in sheet.mysticEyes" class="collapse collapse-arrow bg-base-100">
            <button v-if="isOriginal(eye)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="sheet.mysticEyes.splice(key, 1)">✕</button>
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
                        <p class="col-start-1 row-start-1" >{{ eye.active }}</p>
                        <p class="col-start-1 row-start-2">Cooldown: {{ eye.pivot.current_cooldown }} / {{ eye.cooldown }}</p>
                        <button class="btn btn-outline btn-secondary btn-sm grid-rows-1 col-start-2 row-span-full w-4/12 ml-auto my-auto mr-5" @click="selectTarget(eye.id)">Rolar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>
</template>
