<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
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

function addToSheet(toAdd: Advantage) : void {
    modalRef.value?.close();

    if (advantages.value == null) {
        throw new ToastError("A lista de vantagens está vazia");
    }

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

function possibleAdvantages() : Advantage[] {
    let possibleAdvantages: Advantage[] = [];

    const classMapping: {[key: string]: boolean} = {
        mage: props.sheet.classes.isMage,
        cleric: props.sheet.classes.isCleric,
        vampire: props.sheet.classes.isVampire,
        magiteck: props.sheet.classes.isMagiteck,
        hybrid: props.sheet.classes.isHybrid
    };
    const classes = Object.keys(classMapping).filter(className => classMapping[className]);

    advantages.value.forEach(advantage => {
        if (advantage.class == null || classes.includes(advantage.class)) {
            let shouldAdd = props.sheet.advantages.every(sheetAdvantage => {
                return (sheetAdvantage.name != advantage.name || (sheetAdvantage.name == advantage.name && advantage.level > sheetAdvantage.level));
            })

            if (shouldAdd) {
                possibleAdvantages.push(advantage);
            }
        }
    });

    return possibleAdvantages;
}

</script>

<template>
    <dialog id="advantages_modal" class="modal" ref="modalRef">
        <BaseModal title="Vantagens" subtitle="Loja" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="advantage in possibleAdvantages()"
                    :key="advantage.name + advantage.level"
                    :title="advantage.name"
                    :level="advantage.level"
                    :cost="advantage.cost"
                    @add="addToSheet(advantage)"
                >
                    <p class="leading-relaxed">{{ advantage.description }}</p>
                </ShopItemCard>
            </div>
            <p v-if="possibleAdvantages().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
