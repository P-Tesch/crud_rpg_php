<script setup lang="ts">
import { toRaw } from "vue";
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import RollButton from "@ui/RollButton.vue";
import type { Sheet, SonataAbility, SonataArray } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalSonatas: SonataArray = structuredClone(toRaw(props.sheet.sonatas));

const emit = defineEmits(["sync", "add", "addAbility"]);

function rollSonata(sonataName: string | number, abilityName: number) {
    // TODO
}

function isOriginal(key: string | number) : boolean {
    return !originalSonatas.hasOwnProperty(key);
}

function isOriginalAbility(sonataKey: string | number, ability: SonataAbility) : boolean {
    let original = true;
    originalSonatas[sonataKey]?.abilities.forEach((entry) => {
        if (entry.name == ability.name) {
            original = false;
        }
    });
    return original;
}

function remove(key: string | number) : void {
    delete props.sheet.sonatas[key];
}

function removeAbility(sonataKey: string | number, abilityKey: number, ability: SonataAbility) : void {
    if (!isOriginalAbility(sonataKey, ability)) {
        props.sheet.sonatas[sonataKey].abilities[abilityKey] = structuredClone(originalSonatas[sonataKey].abilities[abilityKey]);
    } else {
        props.sheet.sonatas[sonataKey].abilities.splice(abilityKey, 1);
    }

}

</script>

<template>
    <SectionCard title="Sonatas">
        <template #actions>
            <span class="label-mono">{{ Object.keys(sheet.sonatas).length }} / {{ Math.ceil(sheet.stats['lineage'] / 3) }}</span>
        </template>
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="sonata, key in sheet.sonatas" :key="key" :title="String(key)">
                <template #actions>
                    <RemoveButton v-if="isOriginal(key)" @click="remove(key)" />
                </template>
                <CollapseItem v-for="ability, k in sonata.abilities" :key="k" :title="ability.name">
                    <template #meta>
                        <span class="badge badge-xs badge-soft badge-primary font-mono">Nv {{ ability.level }}</span>
                    </template>
                    <template #actions>
                        <RollButton @click="rollSonata(key, k)" />
                        <RemoveButton v-if="isOriginalAbility(key, ability)" @click="removeAbility(key, k, ability)" />
                    </template>
                    <p class="leading-relaxed">{{ ability.description }}</p>
                </CollapseItem>
                <AddButton @click="$emit('addAbility', key)">Adicionar habilidade</AddButton>
            </CollapseItem>
            <p v-if="Object.keys(sheet.sonatas).length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Nenhuma sonata.</p>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>
</template>
