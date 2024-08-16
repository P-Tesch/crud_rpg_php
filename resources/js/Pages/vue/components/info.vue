<script setup lang="ts">
import { ref } from "vue";
import CreationPoints from './creationPoints.vue';
import TextInputModal from './modals/textInputModal.vue';
import TextAreaModal from './modals/textAreaModal.vue';
import type { Sheet } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const points = ref<InstanceType<typeof CreationPoints>>();
const nameModal = ref<InstanceType<typeof TextInputModal>>();
const descriptionModal = ref<InstanceType<typeof TextAreaModal>>();
const backgroundModal = ref<InstanceType<typeof TextAreaModal>>();
const portraitInput = ref<HTMLInputElement>();
defineExpose({ points });

const alignments = {
    "fire": "Fogo",
    "water": "Água",
    "air": "Ar",
    "earth": "Terra",
    "arcana": "Arcana",
    "void": "Vazio",
    "ice": "Gelo",
    "electricity": "Eletricidade"
}

const organizations = {
    "executors": "Executores",
    "brotherhood": "Irmandade",
    "chivalry": "Cavaleiros",
    "exorcists": "Exorcistas"
}

function editName(name: string) : void {
    if (name != null) {
        props.sheet.name = name;
    }
}

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

function updatePortrait() : void {
    if (portraitInput.value == null) {
        return;
    }

    if (portraitInput.value.files == null) {
        return;
    }

    let fr = new FileReader();
    fr.readAsDataURL(portraitInput.value.files[0]);
    fr.onloadend = () => {
        if (typeof fr.result != "string") {
            return;
        }

        props.sheet.portrait = fr.result;
    };
}

function showModal(modal: InstanceType<typeof TextInputModal | typeof TextAreaModal> | undefined, defaultValue: string) : void {
    if (modal == undefined) {
        return;
    }

    modal.modalRef?.showModal();
    modal.input = defaultValue;
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 h-72 max-h-72 overflow-auto">
        <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
            <h1 class="font-semibold text-2xl">Informações</h1>
        </div>

        <div>
            <div class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" name="info-collapse" />
                <div class="collapse-title text-md font-medium flex gap-5">{{ sheet.name }}
                    <svg class="stroke-neutral-content h-5 w-5 z-10" @click="showModal(nameModal, sheet.name)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                    </svg>
                </div>
                <div class="collapse-content overflow-auto text-center">
                    <label for="portrait-input">
                        <img :src="sheet.portrait" class=" rounded-badge outline outline-primary max-w-32 max-h-32 mx-auto"/>
                    </label>

                    <input id="portrait-input" type="file" class="hidden h-0 w-0" @change="updatePortrait()" ref="portraitInput" accept="image/*" />
                </div>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium">Pontos de criação</div>
            <div class="collapse-content overflow-auto">
                <CreationPoints ref="points" :sheet />
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium flex gap-5">Descrição
                <svg class="stroke-neutral-content h-5 w-5 z-10" @click="showModal(descriptionModal, sheet.description)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                </svg>
            </div>
            <div class="collapse-content overflow-auto">
                <p>{{ sheet.description }}</p>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium flex gap-5">História
                <svg class="stroke-neutral-content h-5 w-5 z-10" @click="showModal(backgroundModal, sheet.background)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                </svg>
            </div>
            <div class="collapse-content overflow-auto">
                <p>{{ sheet.background }}</p>
            </div>
        </div>

        <div v-if="sheet.alignment != null" class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium">Alinhamento</div>
            <div class="collapse-content overflow-auto">
                <p>{{ alignments[sheet.alignment] }}</p>
            </div>
        </div>

        <div v-if="sheet.organization != null" class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium">Organização</div>
            <div class="collapse-content overflow-auto">
                <p>{{ organizations[sheet.organization] }}</p>
            </div>
        </div>
    </div>
    <Teleport to="body">
        <TextInputModal :title="'Insira o nome do personagem'" @end="(name) => editName(name)" ref="nameModal"/>
    </Teleport>
    <Teleport to="body">
        <TextAreaModal :title="'Insira a descrição do personagem'" @end="(description) => editDescription(description)" ref="descriptionModal"/>
    </Teleport>
    <Teleport to="body">
        <TextAreaModal :title="'Insira a história do personagem'" @end="(background) => editBackground(background)" ref="backgroundModal"/>
    </Teleport>
</template>
