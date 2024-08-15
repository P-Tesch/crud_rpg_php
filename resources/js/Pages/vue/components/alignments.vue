<script setup lang="ts">
import { Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet
}

const props: Props = defineProps<Props>();

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

</script>

<template>
    <div>
        <h3 v-if="sheet.alignment != null">Alinhamento</h3>
        <p v-if="sheet.alignment != null">{{ alignments[sheet.alignment] }}</p>
    </div>
</template>
