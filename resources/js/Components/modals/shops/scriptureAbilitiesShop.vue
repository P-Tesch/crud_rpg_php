<script setup lang="ts">
import { ref, onBeforeMount } from "vue"
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
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
        <BaseModal title="Habilidades de escritura" subtitle="Loja" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="scriptureAbility in possibleScriptureAbilities()"
                    :key="scriptureAbility.id"
                    :title="scriptureAbility.name"
                    :level="scriptureAbility.level"
                    :cost="scriptureAbility.cost"
                    @add="addToSheet(scriptureAbility)"
                >
                    <p class="leading-relaxed">{{ scriptureAbility.description }}</p>
                </ShopItemCard>
            </div>
            <p v-if="possibleScriptureAbilities().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
