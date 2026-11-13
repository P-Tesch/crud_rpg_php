<script setup lang="ts">
import { ref, toRaw } from "vue";
import NumberInputModal from '@modals/numberInputModal.vue';
import ToastError from "@scripts/ToastError.ts";
import { AxiosError } from "axios";
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import RollButton from "@ui/RollButton.vue";
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
    <SectionCard title="Escolas">
        <template #actions>
            <span class="label-mono">{{ Object.keys(sheet.schools).length }} / {{ Math.floor(sheet.stats['magic']) + 1 }}</span>
        </template>
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="school, key in sheet.schools" :key="key" :title="String(key)">
                <template #meta>
                    <span class="badge badge-xs badge-soft badge-primary font-mono">Nv {{ school.level }}</span>
                </template>
                <template #actions>
                    <RemoveButton v-if="isOriginal(school, key)" @click="remove(key)" />
                </template>
                <CollapseItem v-for="spell, k in school.spells" :key="k" :title="String(k)">
                    <template #meta>
                        <span class="badge badge-xs badge-ghost">{{ types[spell.type] }}</span>
                    </template>
                    <template #actions>
                        <RollButton @click="rollSpell(key, k)" />
                    </template>
                    <p class="leading-relaxed">{{ spell.description }}</p>
                </CollapseItem>
            </CollapseItem>
            <p v-if="Object.keys(sheet.schools).length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Nenhuma escola aprendida.</p>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>

    <Teleport to="body">
        <NumberInputModal :title="'Custo da magia'" @end="(cost) => roll(cost)" ref="costModal"/>
    </Teleport>
</template>
