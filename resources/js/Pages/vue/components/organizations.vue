<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"

defineProps({ sheet: Object })

const organizations = {
    "": ""
}

</script>

<template>
    <div>
        <h3 v-if="sheet.organization != null">Organização</h3>
        <p v-if="sheet.organization != null">{{ organizations[sheet.organization] }}</p>
    </div>
</template>
