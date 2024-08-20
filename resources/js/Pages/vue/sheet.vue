<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
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
import SonatasTable from './components/sonatas.vue'
import SonatasShop from './components/modals/shops/sonatasShop.vue'
import SonataAbilitiesShop from './components/modals/shops/sonataAbilitiesShop.vue'
import TargetSelectModal from './components/modals/targetSelectModal.vue'
import CreationPoints from './components/creationPoints.vue'
import ErrorHandler from './errorHandler.vue'
import { Sheet } from 'rpgTypes'
import { AxiosError, AxiosResponse } from 'axios'

interface Props {
    sheet: Sheet
}

const props = defineProps<Props>();

const points = ref<InstanceType<typeof CreationPoints>>();
const scripture = ref<InstanceType<typeof Scripture>>();
const gridClass = ref<string>("p-5 grid grid-cols-3 gap-5");

const mysticEyesTable = ref<InstanceType<typeof MysticEyesTable>>();

const schoolsModal = ref<InstanceType<typeof SchoolsShop>>();
const eyesModal = ref<InstanceType<typeof MysticEyesShop>>();
const advantagesModal = ref<InstanceType<typeof AdvantagesShop>>();
const miraclesModal = ref<InstanceType<typeof MiraclesShop>>();
const scriptureAbilitiesModal = ref<InstanceType<typeof ScriptureAbilitiesShop>>();
const sonatasModal = ref<InstanceType<typeof SonatasShop>>();
const sonataAbilitiesModal = ref<InstanceType<typeof SonataAbilitiesShop>>();
const targetModal = ref<InstanceType<typeof TargetSelectModal>>();

const successToast = ref<InstanceType<typeof SuccessToast>>();
const failToast = ref<InstanceType<typeof FailToast>>();

const statsKey = ref<number>(0);
const skillsKey = ref<number>(0);
const miraclesKey = ref<number>(0);
const schoolsKey = ref<number>(0);
const mysticEyesKey = ref<number>(0);
const advantagesKey = ref<number>(0);
const scriptureKey = ref<number>(0);
const sonatasKey = ref<number>(0);

const calculateGridCols = () : void => {
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

onMounted(() : void => {
    calculateGridCols();
    window.addEventListener("resize", () => {
        calculateGridCols();
    })
});

function persist() : void {
    if (this.points.points.remainingPoints < 0 || (this.scripture != null && this.scripture.remainingPoints < 0)) {
        throw new Error("Pontos de criação insuficientes");
    }

    const url: string = "/api/sheets";

    window.axios.put(url, props.sheet)
        .then(() => {
            updateSheet();
            this.statsKey++;
            this.skillsKey++;
            this.miraclesKey++;
            this.schoolsKey++;
            this.mysticEyesKey++;
            this.advantagesKey++;
            this.scriptureKey++;
            this.sonatasKey++;

            this.successToast.toastRef = true;
            setTimeout(() => this.successToast.toastRef = false, 2500);
        }
    ).catch((e: AxiosError) => {
        window.document.body.innerHTML = JSON.stringify(e.response?.data);
            throw new Error("Falha ao salvar ficha");
        }
    );
}

async function updateSheet() : Promise<void> {
    const url: string = "api/sheets";

    window.axios.get(url)
        .then((response: AxiosResponse) => {
            const sheet: Sheet = response.data;
            props.sheet.attributes = sheet.attributes;
            props.sheet.maxAttributes = sheet.maxAttributes;
            props.sheet.portrait = sheet.portrait;
        }
    ).catch(() => {
            throw new Error("Falha ao atualizar ficha");
        }
    );
}

function endTurn() : void {
    console.log(props.sheet);
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
        <SchoolsTable :sheet @add="schoolsModal.modalRef.showModal()" @sync="updateSheet()" v-if="sheet.classes['isMage']" :key="schoolsKey"/>
        <SonatasTable :sheet @add="sonatasModal?.modalRef?.showModal()" @addAbility="(sonataId: string) => sonataAbilitiesModal?.build(sonataId)" v-if="sheet.classes['isVampire']" :key="sonatasKey" />
        <Scripture :sheet :key="scriptureKey" v-if="sheet.classes['isCleric']" @add="scriptureAbilitiesModal.modalRef.showModal()" ref="scripture" />
        <ItemsTable :sheet @sync="updateSheet()" />
        <MysticEyesTable :sheet @add="eyesModal.modalRef.showModal()" @target="targetModal.updateCharacters(); targetModal.modalRef.showModal()" :key="mysticEyesKey" ref="mysticEyesTable" />
        <Advantages :sheet @add="advantagesModal.modalRef.showModal()" :key="advantagesKey" />
        <MiraclesTable :sheet @add="miraclesModal.modalRef.showModal()" v-if="sheet.classes['isCleric']" :key="miraclesKey" />

        <div class="fixed bottom-10 right-10 space-x-5 z-10">
            <button class="btn btn-outline btn-accent" id="save" @click="persist()">Salvar</button>
            <button class="btn btn-outline btn-secondary" @click="endTurn()">Terminar turno</button>
        </div>
    </div>

    <Teleport to="body" v-if="props.sheet.classes.isMage" >
        <SchoolsShop :sheet ref="schoolsModal" />
    </Teleport>
    <Teleport to="body">
        <MysticEyesShop :sheet ref="eyesModal" />
    </Teleport>
    <Teleport to="body">
        <AdvantagesShop :sheet ref="advantagesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isCleric">
        <MiraclesShop :sheet ref="miraclesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isCleric">
        <ScriptureAbilitiesShop :sheet ref="scriptureAbilitiesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isVampire">
        <SonatasShop :sheet ref="sonatasModal" />
    </Teleport v-if="props.sheet.classes.isVampire">
    <Teleport to="body">
        <SonataAbilitiesShop :sheet ref="sonataAbilitiesModal" />
    </Teleport>
    <Teleport to="body">
        <TargetSelectModal :sheet ref="targetModal" @end="(id: number) => mysticEyesTable.rollMysticEye(id)"/>
    </Teleport>

    <SuccessToast class="z-10" ref="successToast" :message="'Ficha salva com sucesso'" />
    <ErrorHandler />
</template>
