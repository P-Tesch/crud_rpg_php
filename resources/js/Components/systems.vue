<script setup lang="ts">
import { toRaw } from "vue";
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import RollButton from "@ui/RollButton.vue";
import type { Sheet, SonataAbility, SonataArray, Subsystem, SystemArray } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalSystems: SystemArray = structuredClone(toRaw(props.sheet.systems));

const emit = defineEmits(["sync", "add", "addSubsystem"]);

function isOriginal(key: string | number) : boolean {
    return !originalSystems.hasOwnProperty(key);
}

function isOriginalSubsystem(SystemKey: string | number, subsystem: Subsystem) : boolean {
    let original = true;
    originalSystems[SystemKey]?.subsystems.forEach((entry) => {
        if (entry.name == subsystem.name) {
            original = false;
        }
    });
    return original;
}

function remove(key: string | number) : void {
    delete props.sheet.systems[key];
}

function removeSubsystem(systemKey: string | number, subsystemKey: number) : void {
    props.sheet.systems[systemKey].subsystems.splice(subsystemKey, 1);

}

</script>

<template>
    <SectionCard title="Sistemas">
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="system, key in sheet.systems" :key="key" :title="String(key)">
                <template #actions>
                    <button type="button" class="btn btn-xs btn-soft btn-accent">Ativar</button>
                    <button type="button" class="btn btn-xs btn-ghost text-error hover:bg-error/10">Queimar</button>
                    <RemoveButton v-if="isOriginal(key)" @click="remove(key)" />
                </template>
                <CollapseItem v-for="subsystem, k in system.subsystems" :key="k" :title="subsystem.name">
                    <template #actions>
                        <RemoveButton v-if="isOriginalSubsystem(key, subsystem)" @click="removeSubsystem(key, k)" />
                    </template>
                    <p class="leading-relaxed">{{ subsystem.description }}</p>
                    <div class="flex flex-wrap justify-end gap-1.5">
                        <button type="button" class="btn btn-xs btn-soft btn-accent">Ativar</button>
                        <button type="button" class="btn btn-xs btn-ghost text-error hover:bg-error/10">Queimar</button>
                        <RollButton />
                    </div>
                </CollapseItem>
                <AddButton @click="$emit('addSubsystem', key)">Adicionar subsistema</AddButton>
            </CollapseItem>
            <p v-if="Object.keys(sheet.systems).length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Nenhum sistema instalado.</p>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>
</template>
