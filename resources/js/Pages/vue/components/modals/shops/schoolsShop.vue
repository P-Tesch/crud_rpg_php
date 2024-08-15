<script setup lang="ts">
import { ref, Ref, onBeforeMount } from "vue";
import type { Sheet, School, SchoolFromShop, SpellArray } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props: Props = defineProps<Props>();
const modalRef: Ref<HTMLDialogElement | null> = ref(null);
const schools: Ref<SchoolFromShop[] | null> = ref(null);

const types: {[key: string]: string} = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    "null": "Outro"
}

onBeforeMount(() => {getSchools()})

defineExpose({modalRef});

async function getSchools() : Promise<void> {
    const url: string = "/api/schools";
    try {
        const response: Response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        schools.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        let open: Window | null = window.open();

        if (open != null) {
            open.document.body.innerHTML = error.message;
        }
    }
}

function addToSheet(index: number) : void {
    if (schools.value == null) {
        return;
    }

    if (props.sheet.schools.length == 0) {
        props.sheet.schools = {}
    }
    let toAdd: SchoolFromShop = schools.value[index];

    let original: School = props.sheet.schools[toAdd.name];
    if (original != null && original.level >= toAdd.level) {
        return;
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
            "level": toAdd.level,
            "cost": toAdd.cost,
            "spells": spells
        };
    }
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
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="school, key in schools">
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
                    <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
