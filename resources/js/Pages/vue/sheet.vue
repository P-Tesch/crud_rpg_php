<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, getCurrentInstance } from 'vue'
import SkillsTable from './components/skills.vue'
import StatsTable from './components/stats.vue'
import AttributesTable from './components/attributes.vue'
import SchoolsTable from './components/schools.vue'
import ItemsTable from './components/items.vue'
import MysticEyesTable from './components/mysticEyes.vue'
import RollHistory from './components/rollHistory.vue'
import CharacterInfo from './components/info.vue'
import SchoolsShop from './components/schoolsShop.vue'

defineProps({ sheet: Object })
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const points = ref(null);
const rolls = ref(null);
const schoolsModal = ref(null);

async function persist(sheet) {
    if (points.value.remainingPoints < 0) {
        alert("Pontos de criação insuficientes");
        return;
    }

    const url = /*window.location.host + */"/api/sheets";
    try {
        const response = await fetch(url, {
            method: "PUT",
            headers: {
                "X-CSRF-Token": csrf,
                "Content-Type": "application/json"
            },
            credentials: "same-origin",
            body: JSON.stringify(sheet)
        });
        if (!response.ok) {
            throw new Error(await response.text());
        }

        alert("success");
    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

async function updateSheet(sheet) {
    const url = "api/sheets";
    try {
        const response = await fetch(url, {
            method: "GET"
        });

        if (!response.ok) {
            throw new Error(await response.text());
        }

        const obj = JSON.parse(await response.text());
        sheet.attributes = obj.attributes;

    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

</script>

<template>
  <Layout>
    <Head title="Ficha" />
    <div class="p-5 grid grid-cols-3 gap-5">
        <CharacterInfo :sheet ref="points" />
        <AttributesTable :sheet />
        <StatsTable :sheet />
        <SkillsTable :sheet :rolls />
        <SchoolsTable :sheet :rolls @add="schoolsModal.modalRef.showModal()" @sync="updateSheet(sheet)" />
        <ItemsTable :sheet />
        <MysticEyesTable :sheet />
        <RollHistory class="col-start-3 row-start-1" ref="rolls" :sheet />
    <!--
        <div>
            <h3>{{ sheet }}</h3>
        </div>
    -->
        <div class="fixed bottom-10 right-10 space-x-5">
            <button class="btn btn-outline btn-accent" id="save" @click="persist(sheet)">Salvar</button>
            <button class="btn btn-outline btn-secondary">Terminar turno</button>
            <input type="hidden" name="_token" :value="csrf">
        </div>
    </div>
    <Teleport to="body">
        <SchoolsShop :sheet ref="schoolsModal"/>
    </Teleport>
  </Layout>
</template>
