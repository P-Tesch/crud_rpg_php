<script setup lang="ts">
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import type { Sheet, Advantage } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalAdvantages: Advantage[] = Object.values(Object.assign({}, props.sheet.advantages));

const emit = defineEmits(["add"]);

function isOriginal(advantage: Advantage) : boolean {
    let isOriginal: boolean = false;
    originalAdvantages.forEach(
        (original) => {
            if (original.name == advantage.name && original.level == advantage.level) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

function remove(key: number, toRemove: Advantage) : void {
    let isPresent: boolean = false;
    originalAdvantages.forEach(
        (original) => {
            if (toRemove.name == original.name) {
                isPresent = true;
                props.sheet.advantages[key] = original;
            }
        }
    );

    if (!isPresent) {
        props.sheet.advantages.splice(key, 1);
    }
}

</script>

<template>
    <SectionCard title="Vantagens">
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="advantage, key in sheet.advantages" :key="advantage.name" :title="advantage.name">
                <template #meta>
                    <span class="badge badge-xs badge-soft badge-primary font-mono">Nv {{ advantage.level }}</span>
                </template>
                <template #actions>
                    <RemoveButton v-if="isOriginal(advantage)" @click="remove(key, advantage)" />
                </template>
                <p class="leading-relaxed">{{ advantage.description }}</p>
            </CollapseItem>
            <p v-if="sheet.advantages.length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Nenhuma vantagem.</p>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>
</template>
