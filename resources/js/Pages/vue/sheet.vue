<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
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
import Scripture from './components/scripture.vue'
import ScriptureAbilitiesShop from './components/modals/shops/scriptureAbilitiesShop.vue'
import TargetSelectModal from './components/modals/targetSelectModal.vue'

defineProps({ sheet: Object })
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const points = ref(null);
const scripture = ref(null);
const gridClass = ref("p-5 grid grid-cols-3 gap-5");

const mysticEyesTable = ref(null);

const schoolsModal = ref(null);
const eyesModal = ref(null);
const advantagesModal = ref(null);
const miraclesModal = ref(null);
const scriptureAbilitiesModal = ref(null);
const targetModal = ref(null);

const successToast = ref(null);
const failToast = ref(null);

const statsKey = ref(0);
const skillsKey = ref(0);
const miraclesKey = ref(0);
const schoolsKey = ref(0);
const mysticEyesKey = ref(0);
const advantagesKey = ref(0);
const scriptureKey = ref(0);

const calculateGridCols = () => {
    if (window.innerWidth >= 1400) {
        gridClass.value = "p-5 grid grid-cols-3 gap-5";
        return;
    }

    if (window.innerWidth >= 910) {
        gridClass.value = "p-5 grid grid-cols-2 gap-5";
        return;
    }

    gridClass.value = "p-5 grid grid-cols-1 gap-5";
}

onMounted(() => {
    calculateGridCols();
    window.addEventListener("resize", () => {
        calculateGridCols();
    })
});

async function persist(sheet) {
    if (this.points.points.remainingPoints < 0 || this.scripture.remainingPoints < 0) {
        this.failToast.toastRef = true;
        setTimeout(() => this.failToast.toastRef = false, 2500);
        return;
    }

    router.put("/api/sheets", JSON.stringify(sheet));

    updateSheet(sheet);
    this.statsKey++;
    this.skillsKey++;
    this.miraclesKey++;
    this.schoolsKey++;
    this.mysticEyesKey++;
    this.advantagesKey++;
    this.scriptureKey++;

    this.successToast.toastRef = true;
    setTimeout(() => this.successToast.toastRef = false, 2500);
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
    console.log(sheet);
}

</script>

<template>
    <Head title="Ficha" />
    <div :class="gridClass">
        <CharacterInfo :sheet ref="points" />
        <AttributesTable :sheet />
        <RollHistory :sheet />
        <StatsTable :sheet :key="statsKey" />
        <SkillsTable :sheet :key="skillsKey" />
        <SchoolsTable :sheet @add="schoolsModal.modalRef.showModal()" @sync="updateSheet(sheet)" v-if="sheet.classes['isMage']" :key="schoolsKey"/>
        <Scripture :sheet :key="scriptureKey" v-if="sheet.classes['isCleric']" @add="scriptureAbilitiesModal.modalRef.showModal()" ref="scripture" />
        <ItemsTable :sheet @sync="updateSheet(sheet)" />
        <MysticEyesTable :sheet @add="eyesModal.modalRef.showModal()" @target="targetModal.updateCharacters(); targetModal.modalRef.showModal()" :key="mysticEyesKey" ref="mysticEyesTable" />
        <Advantages :sheet @add="advantagesModal.modalRef.showModal()" :key="advantagesKey" />
        <MiraclesTable :sheet @add="miraclesModal.modalRef.showModal()" v-if="sheet.classes['isCleric']" :key="miraclesKey" />

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
    <Teleport to="body">
        <ScriptureAbilitiesShop :sheet ref="scriptureAbilitiesModal" />
    </Teleport>
    <Teleport to="body">
        <TargetSelectModal :sheet :csrf ref="targetModal" @end="(id) => mysticEyesTable.rollMysticEye(id)"/>
    </Teleport>

    <SuccessToast class="z-10" ref="successToast" :message="'Ficha salva com sucesso'" />
    <FailToast class="z-10" ref="failToast" :message="'Pontos de criação insuficientes'" />
</template>
