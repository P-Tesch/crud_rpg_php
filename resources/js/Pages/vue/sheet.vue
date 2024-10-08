<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import SkillsTable from '@components/skills.vue'
import StatsTable from '@components/stats.vue'
import AttributesTable from '@components/attributes.vue'
import SchoolsTable from '@components/schools.vue'
import ItemsTable from '@components/items.vue'
import MysticEyesTable from '@components/mysticEyes.vue'
import RollHistory from '@components/rollHistory.vue'
import CharacterInfo from '@components/info.vue'
import SchoolsShop from '@shops/schoolsShop.vue'
import MysticEyesShop from '@shops/mysticEyesShop.vue'
import Advantages from '@components/advantages.vue'
import AdvantagesShop from '@shops/advantagesShop.vue'
import MiraclesTable from '@components/miracles.vue'
import MiraclesShop from '@shops/miraclesShop.vue'
import Scripture from '@components/scripture.vue'
import ScriptureAbilitiesShop from '@shops/scriptureAbilitiesShop.vue'
import SonatasTable from '@components/sonatas.vue'
import SonatasShop from '@shops/sonatasShop.vue'
import SonataAbilitiesShop from '@shops/sonataAbilitiesShop.vue'
import Systems from '@components/systems.vue'
import SystemsShop from '@shops/systemsShop.vue'
import SubsystemsShop from '@shops/subsystemsShop.vue'
import TargetSelectModal from '@modals/targetSelectModal.vue'
import ToastHandler from '@pages/toastHandler.vue'
import ToastError from '@scripts/ToastError.ts'
import FooterComponent from '@components/footer.vue'
import NumberInputModal from '@modals/numberInputModal.vue'
import { AxiosError } from 'axios'
import type { AxiosResponse } from 'axios'
import type { Sheet } from 'rpgTypes'

interface Props {
    sheet: Sheet
}

const props = defineProps<Props>();

const info = ref<InstanceType<typeof CharacterInfo>>();
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
const systemsModal = ref<InstanceType<typeof SystemsShop>>();
const subsystemsModal = ref<InstanceType<typeof SubsystemsShop>>();
const targetModal = ref<InstanceType<typeof TargetSelectModal>>();

const toastHandler = ref<InstanceType<typeof ToastHandler>>();

const statsKey = ref<number>(0);
const skillsKey = ref<number>(0);
const miraclesKey = ref<number>(0);
const schoolsKey = ref<number>(0);
const schoolShopKey = ref<number>(0);
const mysticEyesKey = ref<number>(0);
const mysticEyesShopKey = ref<number>(0);
const advantagesKey = ref<number>(0);
const scriptureKey = ref<number>(0);
const sonatasKey = ref<number>(0);
const systemsKey = ref<number>(0);

const rollModalRef = ref<InstanceType<typeof NumberInputModal>>();

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
    if (
        info.value?.points?.remainingPoints == undefined ||
        info.value.points.remainingPoints < 0 ||
        (scripture != null && scripture.value != undefined && scripture?.value?.remainingPoints < 0)
    ) {
        throw new ToastError("Pontos de criação insuficientes");
    }

    if (Object.keys(props.sheet.schools).length > Math.floor(props.sheet.stats["magic"]) + 1) {
        throw new ToastError("O usuário já possui a quantidade máxima de escolas para sua estatística de magia");
    }

    if (Object.keys(props.sheet.sonatas).length > Math.ceil(props.sheet.stats["lineage"] / 3)) {
        throw new ToastError("O personagem já possui o número máximo de sonatas");
    }

    window.axios.put("/api/sheets", props.sheet)
        .then(() => {
            updateSheet();
            statsKey.value++;
            skillsKey.value++;
            miraclesKey.value++;
            schoolsKey.value++;
            schoolShopKey.value++;
            mysticEyesKey.value++;
            mysticEyesShopKey.value++;
            advantagesKey.value++;
            scriptureKey.value++;
            sonatasKey.value++;
            systemsKey.value++;

            toastHandler.value?.showSuccess("Ficha salva com sucesso");
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao salvar ficha", error);
        }
    );
}

async function updateSheet() : Promise<void> {
    window.axios.get("api/sheets")
        .then((response: AxiosResponse) => {
            const sheet: Sheet = response.data;
            props.sheet.attributes = sheet.attributes;
            props.sheet.maxAttributes = sheet.maxAttributes;
            props.sheet.portrait = sheet.portrait;
        }
    ).catch((error: AxiosError) => {
            throw new ToastError("Falha ao atualizar ficha", error);
        }
    );
}

function endTurn() : void {
    console.log(props.sheet);
}

function rollModal() : void {
    rollModalRef.value?.modalRef?.show();
}

function rollGeneric(value: number) : void {
    window.axios.get("/api/roll/generic", {
            params: {
                modifier: value
            }
        }
    )
        .catch((error: AxiosError) => {
            throw new ToastError("Falha ao rolar dados", error);
        }
    );
}

</script>

<template>
    <Head title="Ficha" />
    <div :class="gridClass">
        <CharacterInfo :sheet ref="info" />
        <AttributesTable :sheet />
        <RollHistory :sheet />
        <StatsTable :sheet :key="statsKey" />
        <SkillsTable :sheet :key="skillsKey" />
        <SchoolsTable :sheet @add="schoolsModal?.modalRef?.showModal()" @sync="updateSheet()" v-if="sheet.classes['isMage']" :key="schoolsKey"/>
        <SonatasTable :sheet @add="sonatasModal?.modalRef?.showModal()" @addAbility="(sonataId: string) => sonataAbilitiesModal?.build(sonataId)" v-if="sheet.classes['isVampire']" :key="sonatasKey" />
        <Scripture :sheet :key="scriptureKey" v-if="sheet.classes['isCleric']" @add="scriptureAbilitiesModal?.modalRef?.showModal()" ref="scripture" />
        <Systems :sheet :key="systemsKey" v-if="sheet.classes['isMagiteck']" @add="systemsModal?.modalRef?.showModal()" @addSubsystem="(systemId: string) => subsystemsModal?.build(systemId)" />
        <ItemsTable :sheet @sync="updateSheet()" />
        <MysticEyesTable :sheet @add="eyesModal?.modalRef?.showModal()" @target="targetModal?.updateCharacters(); targetModal?.modalRef?.showModal()" :key="mysticEyesKey" ref="mysticEyesTable" />
        <Advantages :sheet @add="advantagesModal?.modalRef?.showModal()" :key="advantagesKey" />
        <MiraclesTable :sheet @add="miraclesModal?.modalRef?.showModal()" v-if="sheet.classes['isCleric']" :key="miraclesKey" />
    </div>

    <FooterComponent @persist="persist()" @end-turn="endTurn" @roll-generic="rollModal()" />

    <Teleport to="body">
        <MysticEyesShop :sheet ref="eyesModal" :key="mysticEyesShopKey" />
    </Teleport>
    <Teleport to="body">
        <AdvantagesShop :sheet ref="advantagesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isMage">
        <SchoolsShop :sheet ref="schoolsModal" :key="schoolShopKey" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isCleric">
        <MiraclesShop :sheet ref="miraclesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isCleric">
        <ScriptureAbilitiesShop :sheet ref="scriptureAbilitiesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isVampire">
        <SonatasShop :sheet ref="sonatasModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isVampire">
        <SonataAbilitiesShop :sheet ref="sonataAbilitiesModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isMagiteck">
        <SystemsShop :sheet ref="systemsModal" />
    </Teleport>
    <Teleport to="body" v-if="props.sheet.classes.isMagiteck">
        <SubsystemsShop :sheet ref="subsystemsModal" />
    </Teleport>
    <Teleport to="body">
        <TargetSelectModal :sheet ref="targetModal" @end="(id: number) => mysticEyesTable?.rollMysticEye(id)"/>
    </Teleport>
    <Teleport to="body">
        <NumberInputModal :title="'Insira o número a ser rolado'" @end="value => rollGeneric(value)" ref="rollModalRef"/>
    </Teleport>

    <ToastHandler ref="toastHandler" />
</template>
