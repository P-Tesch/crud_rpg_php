<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
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
    window.axios.get("/api/systems")
        .then((response: AxiosResponse) => {
            systems.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar sistemas", error);
        }
    );
}

function addToSheet(system: SystemFromShop) : void {
    modalRef.value?.close();

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

    if (props.sheet.systems.hasOwnProperty(system.name)) {
        throw new ToastError("O personagem já possui esse sistema");
    }

    props.sheet.systems[system.name] = {
        id: system.id,
        subsystems: []
    };
}

function possibleSystems() : SystemFromShop[] {
    let possibleSystems: SystemFromShop[] = [];

    systems.value.forEach(system => {
        if (props.sheet.systems[system.name] == undefined) {
            possibleSystems.push(system);
        }
    });

    return possibleSystems;
}

</script>

<template>
    <dialog id="systems_modal" class="modal" ref="modalRef">
        <BaseModal title="Sistemas" subtitle="Loja" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="system in possibleSystems()"
                    :key="system.id"
                    :title="system.name"
                    @add="addToSheet(system)"
                >
                    <template #meta>
                        <span v-if="system.damage != null" class="badge badge-sm badge-ghost font-mono">Dano {{ system.damage }}</span>
                    </template>
                    <p class="leading-relaxed">{{ system.description }}</p>
                    <div v-if="system.subsystems.length" class="flex flex-col gap-1.5">
                        <p class="label-mono">Subsistemas</p>
                        <CollapseItem v-for="subsystem in system.subsystems" :key="subsystem.name" :title="subsystem.name">
                            <p class="leading-relaxed">{{ subsystem.description }}</p>
                        </CollapseItem>
                    </div>
                </ShopItemCard>
            </div>
            <p v-if="possibleSystems().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
