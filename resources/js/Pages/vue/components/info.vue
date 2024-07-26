<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"
import CreationPoints from './creationPoints.vue'
import Alignments from './alignments.vue'
import Organizations from './organizations.vue'

const props = defineProps({
    sheet: Object,
    points: Object
})

const points = ref(null);
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

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 h-72 max-h-72 overflow-auto">

        <div class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium">{{ sheet.name }}</div>
            <div class="collapse-content overflow-auto">
                <!--img src={{ sheet.data.portrait }} alt="Retrato"-->
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
            <div class="collapse-title text-md font-medium">Descrição</div>
            <div class="collapse-content overflow-auto">
                <p>{{ sheet.description }}</p>
            </div>
        </div>

        <div class="collapse collapse-arrow bg-base-100">
            <input type="checkbox" name="info-collapse" />
            <div class="collapse-title text-md font-medium">História</div>
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
</template>
