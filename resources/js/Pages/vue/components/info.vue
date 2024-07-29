<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"
import CreationPoints from './creationPoints.vue'
import Alignments from './alignments.vue'
import Organizations from './organizations.vue'
import TextInputModal from './modals/textInputModal.vue'
import TextAreaModal from './modals/textAreaModal.vue'

const props = defineProps({
    sheet: Object,
    points: Object
})

const points = ref(null);
const nameModal = ref(null);
const descriptionModal = ref(null);
const backgroundModal = ref(null);
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
    "": ""
}

function editName(name) {
    if (name != null) {
        props.sheet.name = name;
    }
}

function editDescription(description) {
    if (description != null) {
        props.sheet.description = description;
    }
}

function editBackground(background) {
    if (background != null) {
        props.sheet.background = background;
    }
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 h-72 max-h-72 overflow-auto">

        <div>
            <div class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" name="info-collapse" />
                <div class="collapse-title text-md font-medium flex gap-5">{{ sheet.name }}
                    <svg class="stroke-neutral-content h-5 w-5 z-10" @click="nameModal.modalRef.showModal(); nameModal.input = sheet.name;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                    </svg>
                </div>
                <div class="collapse-content overflow-auto">
                    <!--img src={{ sheet.data.portrait }} alt="Retrato"-->
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
                <svg class="stroke-neutral-content h-5 w-5 z-10" @click="descriptionModal.modalRef.showModal(); descriptionModal.input = sheet.description;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <svg class="stroke-neutral-content h-5 w-5 z-10" @click="backgroundModal.modalRef.showModal(); backgroundModal.input = sheet.background;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
