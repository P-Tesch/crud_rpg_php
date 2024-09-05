<script setup lang="ts">
import { ref, toRaw } from "vue";
import NumberInputModal from '@modals/numberInputModal.vue';
import ToastError from "@scripts/ToastError.ts";
import { AxiosError } from "axios";
import type { AssociativeArray, School, SchoolArray, Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalSchools: SchoolArray = structuredClone(toRaw(props.sheet.schools));

const emit = defineEmits(["sync", "add"]);
const costModal = ref<InstanceType<typeof NumberInputModal>>();

var school: string | number;
var spell: string | number;

const types: AssociativeArray = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    "null": "Outro"
}

function rollSpell(schoolId: string | number, spellId: string | number) : void {
    costModal.value?.modalRef?.showModal();
    school = schoolId;
    spell = spellId;
}

function roll(cost: number) : void {
    if (props.sheet.attributes.mana < cost || cost == null) {
        throw new ToastError("Mana insuficiente");
    }

    window.axios.get(
        "/api/roll/spell", {
            params: {
                school: school,
                spell: spell,
                cost: cost,
                modifier: 0
            }
        }
    ).then(() => {
            emit("sync");
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao rolar magia");
        }
    );
}

function isOriginal(value: School, key: string | number) : boolean {
    let isOriginal: boolean = false;
    if (originalSchools.hasOwnProperty(key)) {
        isOriginal = originalSchools[key].level >= value.level;
    }

    return !isOriginal;
}

function remove(key: string | number) : void {
    let isPresent: boolean = originalSchools.hasOwnProperty(key);

    if (!isPresent) {
        delete props.sheet.schools[key];
    } else {
        props.sheet.schools[key] = structuredClone(originalSchools[key]);
    }
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 flex flex-col justify-between">
        <div>
            <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
                <h1 class="font-semibold text-2xl">Escolas</h1>
            </div>
            <div class="overflow-auto">
                <div v-for="school, key in sheet.schools" class="collapse collapse-arrow bg-base-100">
                    <button v-if="isOriginal(school, key)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="remove(key)">✕</button>
                    <input type="checkbox" name="schools-collapse" />
                    <div class="collapse-title text-xl font-medium">{{ key }} [{{ school.level }}]</div>
                    <div class="collapse-content">
                        <div v-for="spell, k in school.spells" class="collapse collapse-arrow bg-base-100">
                            <input type="checkbox" name="spells-collapse" />
                            <div class="collapse-title text-xl font-medium">{{ k }}</div>
                            <div class="collapse-content">
                                <p>Tipo: {{ types[spell.type] }}</p>
                                <p>Descrição: {{ spell.description }}</p>
                                <button class="btn btn-outline btn-secondary btn-sm" @click="rollSpell(key, k)">Rolar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>

    <Teleport to="body">
        <NumberInputModal :title="'Insira o custo da magia'" @end="(cost) => roll(cost)" ref="costModal"/>
    </Teleport>
</template>
