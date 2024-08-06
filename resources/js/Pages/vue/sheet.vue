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
import SchoolsShop from './components/modals/shops/schoolsShop.vue'
import SuccessToast from './components/alerts/successToast.vue'
import FailToast from './components/alerts/failToast.vue'
import MysticEyesShop from './components/modals/shops/mysticEyesShop.vue'
import Advantages from './components/advantages.vue'
import AdvantagesShop from './components/modals/shops/advantagesShop.vue'
import MiraclesTable from './components/miracles.vue'
import MiraclesShop from './components/modals/shops/miraclesShop.vue'

defineProps({ sheet: Object })
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const points = ref(null);
const rolls = ref(null);
const schoolsModal = ref(null);
const eyesModal = ref(null);
const advantagesModal = ref(null);
const successToast = ref(null);
const failToast = ref(null);
const miraclesModal = ref(null);

async function persist(sheet) {
    if (this.points.points.remainingPoints < 0) {
        this.failToast.toastRef = true;
        setTimeout(() => this.failToast.toastRef = false, 2500);
        return;
    }

    const url = "/api/sheets";
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

        updateSheet(sheet);

        this.successToast.toastRef = true;
        setTimeout(() => this.successToast.toastRef = false, 2500);
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
        sheet.portrait = obj.portrait;

    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function endTurn(sheet) {
    console.log(sheet.portrait);
}

</script>

<template>
    <Head title="Ficha" />
    <div class="p-5 grid grid-cols-3 gap-5">
        <CharacterInfo :sheet ref="points" />
        <AttributesTable :sheet />
        <StatsTable :sheet />
        <SkillsTable :sheet :rolls />
        <SchoolsTable :sheet :rolls @add="schoolsModal.modalRef.showModal()" @sync="updateSheet(sheet)" v-if="sheet.classes['isMage']"/>
        <MiraclesTable :sheet @add="miraclesModal.modalRef.showModal()" v-if="sheet.classes['isCleric']"/>
        <ItemsTable :sheet @sync="updateSheet(sheet)" />
        <MysticEyesTable :sheet @add="eyesModal.modalRef.showModal()" />
        <RollHistory class="col-start-3 row-start-1" ref="rolls" :sheet />
        <Advantages :sheet @add="advantagesModal.modalRef.showModal()" />

        <div class="fixed bottom-10 right-10 space-x-5 z-10">
            <button class="btn btn-outline btn-accent" id="save" @click="persist(sheet)">Salvar</button>
            <button class="btn btn-outline btn-secondary" @click="endTurn(sheet)">Terminar turno</button>
            <input type="hidden" name="_token" :value="csrf">
        </div>
    </div>
    <Teleport to="body">
        <SchoolsShop :sheet ref="schoolsModal" />
    </Teleport>
    <Teleport to="body">
        <MysticEyesShop :sheet ref="eyesModal" />
    </Teleport>
    <Teleport to="body">
        <AdvantagesShop :sheet ref="advantagesModal" />
    </Teleport>
    <Teleport to="body">
        <MiraclesShop :sheet ref="miraclesModal" />
    </Teleport>
    <SuccessToast class="z-10" ref="successToast" :message="'Ficha salva com sucesso'" />
    <FailToast class="z-10" ref="failToast" :message="'Pontos de criação insuficientes'" />
</template>
