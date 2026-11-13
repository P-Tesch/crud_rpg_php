<script setup lang="ts">
import { ref, watch, toRaw } from "vue"
import TextInputModal from '@modals/textInputModal.vue'
import TextAreaModal from '@modals/textAreaModal.vue'
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import RollButton from "@ui/RollButton.vue";
import Stepper from "@ui/Stepper.vue";
import Icon from "@ui/Icon.vue";
import type { Scripture, ScriptureAbility, Sheet } from "rpgTypes";
import ToastError from "@scripts/ToastError.ts";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const emit = defineEmits(["sync", "add"]);

const yesNo: {[key: string | number]: string} = {
    true: "Sim",
    false: "Não",
    0: "Não",
    1: "Sim"
}

const originalScripture: Scripture = structuredClone(toRaw(props.sheet.scripture));

const nameModal = ref<InstanceType<typeof TextInputModal>>();
const descriptionModal = ref<InstanceType<typeof TextAreaModal>>();

const remainingPoints = ref<number>(0);
calculatePoints();
watch(props.sheet.scripture, () => calculatePoints());

defineExpose({remainingPoints});

function calculatePoints() : void {
    let totalAttributes: number = 0;
    let totalAbilities: number = 0;
    let scripture: Scripture = props.sheet.scripture;

    totalAttributes += scripture.damage * 15;
    totalAttributes += scripture.range * 5;
    totalAttributes += scripture.sharpness * 20;
    totalAttributes += Number(scripture.double) * 30;

    props.sheet.scripture.scriptureAbilities.forEach(ability => {
        totalAbilities += ability.cost;
    });

    remainingPoints.value = props.sheet.scripture.creation_points - totalAbilities - totalAttributes;
}

function editName(name: string) : void {
    if (name != null) {
        props.sheet.scripture.name = name;
    }
}

function editDescription(description: string) : void {
    if (description != null) {
        props.sheet.scripture.description = description;
    }
}

function increase(key: string) : void {
    if (typeof props.sheet.scripture[key] != "number") {
        throw new ToastError("Falha ao aumentar atributo de escritura");
    }

    props.sheet.scripture[key]++;
}

function decrease(key: string) : void {
    if (typeof props.sheet.scripture[key] != "number") {
        throw new ToastError("Falha ao diminuitr atributo de escritura");
    }

    props.sheet.scripture[key]--;
}

function canDecrease(key: string) : boolean {
    return originalScripture[key] < props.sheet.scripture[key];
}

function invertDouble() : void {
    props.sheet.scripture.double = !props.sheet.scripture.double;
}

function rollScriptureAbility(ability: ScriptureAbility) {
    console.log("TODO");
}

function showModal(modal: InstanceType<typeof TextInputModal | typeof TextAreaModal> | undefined, defaultValue: string) : void {
    if (modal == undefined) {
        throw new ToastError("Falha ao exibir popup");
    }
    modal.modalRef?.showModal();
    modal.input = defaultValue;
}

function isOriginal(ability: ScriptureAbility) : boolean {
    return originalScripture.scriptureAbilities.every(original => {
        return original.name != ability.name;
    });
}

function remove(key: number) : void {
    props.sheet.scripture.scriptureAbilities.splice(key, 1);
}

</script>


<template>
    <SectionCard title="Escritura">
        <template #actions>
            <span class="label-mono" :class="{ 'text-error': remainingPoints < 0 }">{{ remainingPoints }} / {{ sheet.scripture.creation_points }} pts</span>
        </template>
        <div class="flex flex-col gap-3">
            <div class="rounded-field border border-base-content/8 bg-base-100/50 p-3">
                <div class="flex items-center gap-2">
                    <h3 class="min-w-0 flex-1 truncate font-display font-semibold">{{ sheet.scripture.name || 'Sem nome' }}</h3>
                    <button type="button" class="btn btn-square btn-ghost btn-xs text-base-content/50" aria-label="Editar nome da escritura" @click="showModal(nameModal, sheet.scripture.name)">
                        <Icon name="edit" class="size-3.5" />
                    </button>
                </div>
                <div class="mt-1 flex items-start gap-2">
                    <p class="min-w-0 flex-1 text-sm leading-relaxed text-base-content/75">{{ sheet.scripture.description || 'Sem descrição.' }}</p>
                    <button type="button" class="btn btn-square btn-ghost btn-xs text-base-content/50" aria-label="Editar descrição da escritura" @click="showModal(descriptionModal, sheet.scripture.description)">
                        <Icon name="edit" class="size-3.5" />
                    </button>
                </div>
            </div>

            <ul class="flex flex-col">
                <li class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                    <span class="flex-1 text-sm font-medium">Pontos de criação</span>
                    <span class="font-mono text-lg font-semibold tabular-nums">{{ sheet.scripture.creation_points }}</span>
                    <Stepper
                        label="pontos de criação da escritura"
                        :can-increase="sheet.scripture.creation_points < sheet.stats.faith * 50"
                        :can-decrease="canDecrease('creation_points')"
                        @increase="sheet.scripture.creation_points += 50"
                        @decrease="sheet.scripture.creation_points -= 50"
                    />
                </li>
                <li class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                    <span class="flex-1 text-sm font-medium">Dano</span>
                    <span class="font-mono text-lg font-semibold tabular-nums">{{ sheet.scripture.damage }}</span>
                    <Stepper label="dano" :can-increase="sheet.scripture.damage < 5" :can-decrease="canDecrease('damage')" @increase="increase('damage')" @decrease="decrease('damage')" />
                </li>
                <li class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                    <span class="flex-1 text-sm font-medium">Alcance</span>
                    <span class="font-mono text-lg font-semibold tabular-nums">{{ sheet.scripture.range }}</span>
                    <Stepper label="alcance" :can-increase="sheet.scripture.range < 15" :can-decrease="canDecrease('range')" @increase="increase('range')" @decrease="decrease('range')" />
                </li>
                <li class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                    <span class="flex-1 text-sm font-medium">Afiação</span>
                    <span class="font-mono text-lg font-semibold tabular-nums">{{ sheet.scripture.sharpness }}</span>
                    <Stepper label="afiação" :can-increase="sheet.scripture.sharpness < 5" :can-decrease="canDecrease('sharpness')" @increase="increase('sharpness')" @decrease="decrease('sharpness')" />
                </li>
                <li class="flex items-center gap-3 rounded-field px-2 py-1.5 hover:bg-base-content/4">
                    <label for="checkbox-double" class="flex-1 text-sm font-medium">Dupla</label>
                    <span class="font-mono text-sm text-base-content/60">{{ yesNo[Number(sheet.scripture.double)] }}</span>
                    <input
                        id="checkbox-double"
                        type="checkbox"
                        class="toggle toggle-sm toggle-primary"
                        :checked="Boolean(sheet.scripture.double)"
                        :disabled="Boolean(originalScripture.double)"
                        @click="invertDouble()"
                    />
                </li>
            </ul>

            <div class="flex flex-col gap-1.5">
                <p class="label-mono px-2">Habilidades</p>
                <CollapseItem v-for="ability, key in sheet.scripture.scriptureAbilities" :key="ability.id" :title="ability.name">
                    <template #meta>
                        <span class="badge badge-xs badge-soft badge-primary font-mono">Nv {{ ability.level }}</span>
                    </template>
                    <template #actions>
                        <RollButton @click="rollScriptureAbility(ability)" />
                        <RemoveButton v-if="isOriginal(ability)" @click="remove(key)" />
                    </template>
                    <p class="leading-relaxed">{{ ability.description }}</p>
                </CollapseItem>
            </div>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>

    <Teleport to="body">
        <TextInputModal :title="'Nome da escritura'" @end="(name: string) => editName(name)" ref="nameModal"/>
    </Teleport>
    <Teleport to="body">
        <TextAreaModal :title="'Descrição da escritura'" @end="(description: string) => editDescription(description)" ref="descriptionModal"/>
    </Teleport>
</template>
