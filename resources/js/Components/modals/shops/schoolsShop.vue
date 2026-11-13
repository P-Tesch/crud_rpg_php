<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import BaseModal from "@ui/BaseModal.vue";
import ShopItemCard from "@ui/ShopItemCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, School, SchoolFromShop, SpellArray, AssociativeArray } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const alignments: AssociativeArray = {
    "fire": "Fogo",
    "water": "Água",
    "air": "Ar",
    "earth": "Terra",
    "arcana": "Arcana",
    "void": "Vazio",
    "ice": "Gelo",
    "electricity": "Eletricidade"
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const schools = ref<SchoolFromShop[]>([]);

const types: AssociativeArray = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    "null": "Outro"
}

onBeforeMount(() => {getSchools()})

defineExpose({modalRef});

function getSchools() : void {
    window.axios.get("/api/schools")
        .then((response: AxiosResponse) => {
            schools.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar escolas");
        }
    );
}

function addToSheet(toAdd: SchoolFromShop) : void {
    modalRef.value?.close();

    if (schools.value == null) {
        throw new ToastError("A lista de escolha está vazia");
    }

    if (props.sheet.schools.length != undefined) {
        props.sheet.schools = {}
    }

    if (!Object.hasOwn(props.sheet.schools, toAdd.name) && Object.keys(props.sheet.schools).length >= Math.floor(props.sheet.stats["magic"]) + 1) {
        throw new ToastError("O usuário já possui a quantidade máxima de escolas para sua estatística de magia");
    }

    let original: School = props.sheet.schools[toAdd.name];
    if (original != null && original.level >= toAdd.level) {
        throw new ToastError("O personagem ja possui essa escola com um nível igual ou maior");
    }

    let spells: SpellArray = {};
    toAdd.spells.forEach(spell => {
        spells[spell.name] = {
            "type": spell.type,
            "description": spell.description,
            "strategy": null
        };
    });
    if (original != null) {
        original.level = toAdd.level;
        original.spells = spells;
    }
    else {
        props.sheet.schools[toAdd.name] = {
            "id": toAdd.id,
            "level": toAdd.level,
            "cost": toAdd.cost,
            "spells": spells
        };
    }
}

function getPossibleSchools() : SchoolFromShop[] {
    let possibleSchools: SchoolFromShop[] = [];
    schools.value.forEach(school => {
        if (
            (
                Object.hasOwn(props.sheet.schools, school.name) &&
                school.level <= props.sheet.schools[school.name].level
            ) ||
            school.name.startsWith("Fundamentos gerais - ") && !school.name.endsWith(alignments[props.sheet.alignment ?? "fire"])
        ) {
            return;
        }

        possibleSchools.push(school);
    });

    return possibleSchools;
}

</script>

<template>
    <dialog id="schools_modal" class="modal" ref="modalRef">
        <BaseModal title="Escolas" subtitle="Loja" size="lg">
            <div class="grid gap-3 md:grid-cols-2">
                <ShopItemCard
                    v-for="school in getPossibleSchools()"
                    :key="school.id"
                    :title="school.name"
                    :level="school.level"
                    :cost="school.cost"
                    @add="addToSheet(school)"
                >
                    <p class="leading-relaxed">{{ school.description }}</p>
                    <div v-if="school.spells.length" class="flex flex-col gap-1.5">
                        <p class="label-mono">Magias</p>
                        <CollapseItem v-for="spell in school.spells" :key="spell.name" :title="spell.name">
                            <template #meta>
                                <span class="badge badge-xs badge-ghost">{{ types[spell.type] }}</span>
                            </template>
                            <p class="leading-relaxed">{{ spell.description }}</p>
                        </CollapseItem>
                    </div>
                </ShopItemCard>
            </div>
            <p v-if="getPossibleSchools().length === 0" class="py-8 text-center text-sm text-base-content/45">Nada disponível no momento.</p>
        </BaseModal>
    </dialog>
</template>
