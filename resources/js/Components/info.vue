<script setup lang="ts">
import { ref } from "vue";
import TextAreaModal from '@modals/textAreaModal.vue';
import SectionCard from '@ui/SectionCard.vue';
import Icon from '@ui/Icon.vue';
import ToastError from "@scripts/ToastError.ts";
import type { Sheet } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const descriptionModal = ref<InstanceType<typeof TextAreaModal>>();
const backgroundModal = ref<InstanceType<typeof TextAreaModal>>();

function editDescription(description: string) : void {
    if (description != null) {
        props.sheet.description = description;
    }
}

function editBackground(background: string) : void {
    if (background != null) {
        props.sheet.background = background;
    }
}

function showModal(modal: InstanceType<typeof TextAreaModal> | undefined, defaultValue: string) : void {
    if (modal == undefined) {
        throw new ToastError("Falha ao carregar popup");
    }

    modal.modalRef?.showModal();
    modal.input = defaultValue;
}

</script>

<template>
    <div class="grid gap-4 xl:grid-cols-2">
        <SectionCard title="Descrição">
            <template #actions>
                <button type="button" class="btn btn-ghost btn-xs text-base-content/60" @click="showModal(descriptionModal, sheet.description)">
                    <Icon name="edit" class="size-3.5" /> Editar
                </button>
            </template>
            <p v-if="sheet.description" class="px-2 py-1 leading-relaxed whitespace-pre-line text-base-content/85">{{ sheet.description }}</p>
            <p v-else class="px-2 py-1 text-sm text-base-content/45 italic">Nenhuma descrição ainda.</p>
        </SectionCard>

        <SectionCard title="História">
            <template #actions>
                <button type="button" class="btn btn-ghost btn-xs text-base-content/60" @click="showModal(backgroundModal, sheet.background)">
                    <Icon name="edit" class="size-3.5" /> Editar
                </button>
            </template>
            <p v-if="sheet.background" class="px-2 py-1 leading-relaxed whitespace-pre-line text-base-content/85">{{ sheet.background }}</p>
            <p v-else class="px-2 py-1 text-sm text-base-content/45 italic">Nenhuma história ainda.</p>
        </SectionCard>
    </div>
    <Teleport to="body">
        <TextAreaModal :title="'Descrição do personagem'" @end="(description) => editDescription(description)" ref="descriptionModal"/>
    </Teleport>
    <Teleport to="body">
        <TextAreaModal :title="'História do personagem'" @end="(background) => editBackground(background)" ref="backgroundModal"/>
    </Teleport>
</template>
