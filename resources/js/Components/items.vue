<script setup lang="ts">
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import RollButton from "@ui/RollButton.vue";
import type { Item, Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

defineProps<Props>();
const emit = defineEmits(["sync"]);

async function rollItem(item: Item) : Promise<void> {
    const url: string = "/api/roll/item?item=" + item.id;

    // TODO

    emit("sync");
}

</script>


<template>
    <SectionCard title="Inventário">
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="item in sheet.items" :key="item.id" :title="item.name">
                <template #meta>
                    <span v-if="item.damage != null" class="badge badge-xs badge-ghost font-mono">Dano {{ item.damage }}</span>
                </template>
                <template #actions>
                    <RollButton @click="rollItem(item)" />
                </template>
                <p class="leading-relaxed">{{ item.description }}</p>
            </CollapseItem>
            <p v-if="sheet.items.length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Inventário vazio.</p>
        </div>
    </SectionCard>
</template>
