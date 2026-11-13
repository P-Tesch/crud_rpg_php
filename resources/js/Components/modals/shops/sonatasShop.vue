<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { AxiosResponse } from "axios";
import type { Sheet, SonataFromShop } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const sonatas = ref<SonataFromShop[]>([]);

onBeforeMount(() => {getSonatas()})

defineExpose({modalRef});

function getSonatas() : void {
    window.axios.get("/api/sonatas")
        .then((response: AxiosResponse) => {
            sonatas.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar sonatas", error);
        }
    );
}

function addToSheet(sonata: SonataFromShop) : void {
    modalRef.value?.close();

    if (props.sheet.sonatas.length != undefined) {
        props.sheet.sonatas = {};
    }

    const maxSonatas = Math.ceil(props.sheet.stats["lineage"] / 3);
    if (Object.keys(props.sheet.sonatas).length >= maxSonatas) {
        throw new ToastError("O personagem já possui o número máximo de sonatas");
    }

    if (props.sheet.sonatas.hasOwnProperty(sonata.name)) {
        throw new ToastError("O personagem já possui esssa sonata");
    }

    props.sheet.sonatas[sonata.name] = {
        id: sonata.id,
        abilities: []
    };
}

function getPossibleSonatas() : SonataFromShop[] {
    let possibleSonatas: SonataFromShop[] = [];

    sonatas.value.forEach(sonata => {
        if (!Object.hasOwn(props.sheet.sonatas, sonata.name)) {
            possibleSonatas.push(sonata);
        }
    });

    return possibleSonatas;
}

</script>

<template>
    <dialog id="sonatas_modal" class="modal" ref="modalRef">
        <BaseModal title="Sonatas" subtitle="Loja" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="sonata in getPossibleSonatas()"
                    :key="sonata.id"
                    :title="sonata.name"
                    @add="addToSheet(sonata)"
                >
                    <p class="leading-relaxed">{{ sonata.description }}</p>
                    <div v-if="sonata.abilities?.length" class="flex flex-col gap-1.5">
                        <p class="label-mono">Habilidades</p>
                        <CollapseItem v-for="ability in sonata.abilities" :key="ability.name + ability.level" :title="ability.name">
                            <template #meta>
                                <span class="badge badge-xs badge-ghost font-mono">Nv {{ ability.level }} · {{ ability.cost }}</span>
                            </template>
                            <p class="leading-relaxed">{{ ability.description }}</p>
                        </CollapseItem>
                    </div>
                </ShopItemCard>
            </div>
            <p v-if="getPossibleSonatas().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
