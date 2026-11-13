<script setup lang="ts">
import { AxiosError } from "axios";
import { ref } from "vue";
import ToastError from "@scripts/ToastError.ts";
import SectionCard from "@ui/SectionCard.vue";
import CollapseItem from "@ui/CollapseItem.vue";
import AddButton from "@ui/AddButton.vue";
import RemoveButton from "@ui/RemoveButton.vue";
import RollButton from "@ui/RollButton.vue";
import type { MysticEye, Sheet } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalMysticEyes: MysticEye[] = Object.assign({}, props.sheet.mysticEyes);
const selectedEye = ref<number>();

const rollMysticEye = (target: number) : void => {
    window.axios.get(
        "/api/roll/mystic_eyes", {
            params: {
                eye: selectedEye.value,
                target: target,
                modifier: 0
            }
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao rolar habilidade de olhos místicos", error);
        }
    );
};

defineExpose({rollMysticEye});

const emit = defineEmits(["add", "target"]);

function selectTarget(eyeId: number) : void {
    selectedEye.value = eyeId;
    emit("target");
}

function isOriginal(eye: MysticEye) : boolean {
    let originalArray = Object.values(originalMysticEyes);
    let isOriginal: boolean = false;
    originalArray.forEach(
        (original) => {
            if (original.name == eye.name) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

</script>

<template>
    <SectionCard title="Olhos místicos">
        <div class="flex flex-col gap-1.5">
            <CollapseItem v-for="eye, key in sheet.mysticEyes" :key="eye.id" :title="eye.name">
                <template #meta>
                    <span v-if="eye.active != null" class="badge badge-xs badge-ghost font-mono">{{ eye.pivot.current_cooldown }} / {{ eye.cooldown }}</span>
                </template>
                <template #actions>
                    <RollButton v-if="eye.active != null" @click="selectTarget(eye.id)" />
                    <RemoveButton v-if="isOriginal(eye)" @click="sheet.mysticEyes.splice(key, 1)" />
                </template>
                <div v-if="eye.passive != null">
                    <p class="label-mono mb-1">Passivo</p>
                    <p class="leading-relaxed">{{ eye.passive }}</p>
                </div>
                <div v-if="eye.active != null">
                    <p class="label-mono mb-1">Ativo</p>
                    <p class="leading-relaxed">{{ eye.active }}</p>
                    <p class="mt-1 font-mono text-xs text-base-content/55">Recarga: {{ eye.pivot.current_cooldown }} / {{ eye.cooldown }}</p>
                </div>
            </CollapseItem>
            <p v-if="sheet.mysticEyes.length === 0" class="px-2 py-3 text-sm text-base-content/45 italic">Nenhum olho místico.</p>
        </div>
        <template #footer>
            <AddButton @click="$emit('add')" />
        </template>
    </SectionCard>
</template>
