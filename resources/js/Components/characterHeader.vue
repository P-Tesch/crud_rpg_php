<script setup lang="ts">
import { computed, ref } from "vue";
import CreationPoints from '@components/creationPoints.vue';
import Vitals from '@components/vitals.vue';
import TextInputModal from '@modals/textInputModal.vue';
import Icon from '@ui/Icon.vue';
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, AssociativeArray } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const points = ref<InstanceType<typeof CreationPoints>>();
const nameModal = ref<InstanceType<typeof TextInputModal>>();
const portraitInput = ref<HTMLInputElement>();
defineExpose({ points });

const alignments: AssociativeArray = {
    "fire": "Fogo",
    "water": "Água",
    "air": "Ar",
    "earth": "Terra",
    "arcana": "Arcana",
    "void": "Vazio",
    "ice": "Gelo",
    "electricity": "Eletricidade"
}

const organizations: AssociativeArray = {
    "executors": "Executores",
    "brotherhood": "Irmandade",
    "chivalry": "Cavaleiros",
    "exorcists": "Exorcistas"
}

const classLabels: AssociativeArray = {
    "isMage": "Mago",
    "isCleric": "Clérigo",
    "isVampire": "Vampiro",
    "isMagiteck": "Magiteck",
    "isHybrid": "Mestiço"
}

const activeClasses = computed<string[]>(() =>
    Object.entries(props.sheet.classes)
        .filter(([, isActive]) => isActive)
        .map(([key]) => classLabels[key] ?? key)
);

function editName(name: string) : void {
    if (name != null) {
        props.sheet.name = name;
    }
}

function openNameModal() : void {
    if (nameModal.value == undefined) {
        throw new ToastError("Falha ao carregar popup");
    }

    nameModal.value.modalRef?.showModal();
    nameModal.value.input = props.sheet.name;
}

function updatePortrait() : void {
    if (portraitInput.value == null || portraitInput.value.files == null) {
        throw new ToastError("Falha ao atualizar retrato");
    }

    let fr = new FileReader();
    fr.readAsDataURL(portraitInput.value.files[0]);

    fr.onloadend = () => {
        if (typeof fr.result != "string") {
            throw new ToastError("Retrato inválido");
        }

        props.sheet.portrait = fr.result;
    };
}

</script>

<template>
    <section class="hud-frame chamfer p-px">
        <div class="chamfer bg-base-200/85 p-4 backdrop-blur-sm sm:p-5">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-4">
                <label for="portrait-input" class="group relative block size-20 shrink-0 cursor-pointer sm:size-24" title="Alterar retrato">
                    <span class="chamfer-sm hud-frame absolute inset-0 p-px">
                        <span class="chamfer-sm block size-full overflow-hidden bg-base-300">
                            <img v-if="sheet.portrait" :src="sheet.portrait" alt="" class="size-full object-cover" />
                            <Icon v-else name="user" class="m-auto mt-6 size-8 text-base-content/30 sm:mt-8" />
                        </span>
                    </span>
                    <span class="chamfer-sm absolute inset-px flex items-center justify-center bg-base-100/70 opacity-0 transition-opacity group-hover:opacity-100">
                        <Icon name="camera" class="size-5" />
                    </span>
                </label>
                <input id="portrait-input" ref="portraitInput" type="file" class="hidden" accept="image/*" @change="updatePortrait()" />

                <div class="min-w-0 flex-1">
                    <p class="label-mono">Personagem</p>
                    <div class="mt-1 flex items-center gap-2">
                        <h1 class="truncate font-display text-2xl font-semibold tracking-tight sm:text-3xl">
                            {{ sheet.name || 'Sem nome' }}
                        </h1>
                        <button type="button" class="btn btn-square btn-ghost btn-sm text-base-content/50 hover:text-base-content" aria-label="Editar nome" title="Editar nome" @click="openNameModal()">
                            <Icon name="edit" />
                        </button>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <span v-for="className in activeClasses" :key="className" class="badge badge-sm badge-soft badge-primary font-medium">{{ className }}</span>
                        <span v-if="sheet.alignment != null" class="badge badge-sm badge-ghost">
                            <span class="text-base-content/50">Alinhamento</span> {{ alignments[sheet.alignment] }}
                        </span>
                        <span v-if="sheet.organization != null" class="badge badge-sm badge-ghost">
                            <span class="text-base-content/50">Organização</span> {{ organizations[sheet.organization] }}
                        </span>
                    </div>
                </div>

                <CreationPoints ref="points" :sheet class="w-full border-t border-base-content/8 pt-4 sm:w-auto sm:border-t-0 sm:border-l sm:pt-0 sm:pl-6" />
            </div>

            <div class="mt-5 border-t border-base-content/8 pt-4">
                <Vitals :sheet />
            </div>
        </div>
    </section>
    <Teleport to="body">
        <TextInputModal :title="'Nome do personagem'" @end="(name) => editName(name)" ref="nameModal"/>
    </Teleport>
</template>
