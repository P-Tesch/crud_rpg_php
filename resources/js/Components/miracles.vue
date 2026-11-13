<script setup lang="ts">
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import type { Miracle, Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalMiracles: Miracle[] = Object.assign({}, props.sheet.miracles);

const emit = defineEmits(["add"]);

function isOriginal(miracle: Miracle) : boolean {
    let originalArray = Object.values(originalMiracles);
    let isOriginal: boolean = false;
    originalArray.forEach(
        (original) => {
            if (original.name == miracle.name) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

</script>

<template>
    <SectionCard title="Milagres">
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="miracle, key in sheet.miracles" :key="miracle.id" :title="miracle.name">
                <template #actions>
                    <RemoveButton v-if="isOriginal(miracle)" @click="sheet.miracles.splice(key, 1)" />
                </template>
                <p class="leading-relaxed">{{ miracle.description }}</p>
            </CollapseItem>
            <p v-if="sheet.miracles.length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Nenhum milagre.</p>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>
</template>
