<script setup lang="ts">
import { ref, onBeforeMount } from "vue"
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { ScriptureAbility, Sheet } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const scriptureAbilities = ref<ScriptureAbility[]>([]);

onBeforeMount(() => { getScriptureAbilities() });

defineExpose({ modalRef });

function getScriptureAbilities() : void {
    window.axios.get("/api/scripture_abilities")
        .then((response: AxiosResponse) => {
            scriptureAbilities.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao buscar habilidades de escritura", error);
        }
    );
}

function addToSheet(toAdd: ScriptureAbility) : void {
    modalRef.value?.close();

    if (scriptureAbilities.value == null) {
        throw new ToastError("A lista de habilidades de escritura está vazia");
    }

    let original: ScriptureAbility[] = props.sheet.scripture.scriptureAbilities;

    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                if (value.level >= toAdd.level) {
                    throw new ToastError("A escritura ja possui essa habilidade com um nível igual ou maior");
                } else {
                    props.sheet.scripture.scriptureAbilities.splice(props.sheet.scripture.scriptureAbilities.indexOf(value), 1);
                }
            }
        }
    );

    props.sheet.scripture.scriptureAbilities.push(toAdd);
}

function possibleScriptureAbilities() : ScriptureAbility[] {
    let possibleScriptureAbilities: ScriptureAbility[] = [];

    scriptureAbilities.value.forEach(ability => {
        let shouldAdd = props.sheet.scripture.scriptureAbilities.every(sheetAbility => {
            return sheetAbility.name != ability.name || (sheetAbility.name == ability.name && ability.level > sheetAbility.level);
        });

        if (shouldAdd) {
            possibleScriptureAbilities.push(ability);
        }
    });

    return possibleScriptureAbilities;
}

</script>

<template>
    <dialog id="scripture_abilities_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Habilidades de escritura</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="scriptureAbility in possibleScriptureAbilities()">
                    <p class="text-2xl">{{ scriptureAbility.name }}</p>
                    <p class="text-md"> {{ scriptureAbility.description }}</p>
                    <p class="text-md">Level: {{ scriptureAbility.level }}</p>
                    <p class="text-md">Custo: {{ scriptureAbility.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end"
                        @click="addToSheet(scriptureAbility)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
