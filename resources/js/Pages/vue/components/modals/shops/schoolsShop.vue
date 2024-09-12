<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
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
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Escolas</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="school in getPossibleSchools()">
                    <h4 class="text-xl font-semibold">{{ school.name }}</h4>
                    <p>{{ school.description }}</p>
                    <p>Level: {{ school.level }}</p>
                    <div v-for="spell in school.spells" class="collapse collapse-arrow bg-base-100">
                        <input type="checkbox" name="spells-collapse" />
                        <div class="collapse-title text-xl font-medium">{{ spell.name }}</div>
                        <div class="collapse-content">
                            <p>Tipo: {{ types[spell.type] }}</p>
                            <p>Descrição: {{ spell.description }}</p>
                        </div>
                    </div>
                    <p>Custo: {{ school.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(school)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
