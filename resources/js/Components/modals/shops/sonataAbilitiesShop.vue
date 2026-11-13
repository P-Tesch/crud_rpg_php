<script setup lang="ts">
import { ref } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, SonataAbility } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const sonataAbilities = ref<SonataAbility[]>([]);
const sonataNameRef = ref<string>("");

defineExpose({modalRef, build});

function build(sonataName: string) {
    window.axios.get("/api/sonata_abilities", {
        params: {
            sonata_name: sonataName
        }
    }).then((response: AxiosResponse) => {
            sonataNameRef.value = sonataName;
            sonataAbilities.value = response.data["data"];
            modalRef.value?.showModal();
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar habilidades de sonata", error);
    }
);
}

function addToSheet(ability: SonataAbility) : void {
    modalRef.value?.close();

    props.sheet.sonatas[sonataNameRef.value].abilities.forEach((entry, key) => {
        if (entry.name == ability.name) {
            if (entry.level >= ability.level) {
                throw new ToastError("O personagem já possui essa habilidade em um nível igual ou maior");
            }

            delete props.sheet.sonatas[sonataNameRef.value].abilities[key];
        }
    });

    props.sheet.sonatas[sonataNameRef.value].abilities.push(ability);
}

function possibleAbilities() : SonataAbility[] {
    let possibleAbilities: SonataAbility[] = [];

    sonataAbilities.value.forEach(ability => {
        let shouldPush = true;

        Object.values(props.sheet.sonatas).every(sonata => {
            return sonata.abilities.every(sheetAbility => {
                shouldPush = sheetAbility.name != ability.name || ability.level > sheetAbility.level;
                return shouldPush;
            });
        });

        if (shouldPush) {
            possibleAbilities.push(ability);
        }
    });

    return possibleAbilities;
}

</script>

<template>
    <dialog id="sonata_abilities_modal" class="modal" ref="modalRef">
        <BaseModal title="Habilidades de sonata" :subtitle="sonataNameRef" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="ability in possibleAbilities()"
                    :key="ability.name + ability.level"
                    :title="ability.name"
                    :level="ability.level"
                    :cost="ability.cost"
                    @add="addToSheet(ability)"
                >
                    <p class="leading-relaxed">{{ ability.description }}</p>
                </ShopItemCard>
            </div>
            <p v-if="possibleAbilities().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
