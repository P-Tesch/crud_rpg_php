<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import SkillsTable from '@components/skills.vue'
import StatsTable from '@components/stats.vue'
import SchoolsTable from '@components/schools.vue'
import ItemsTable from '@components/items.vue'
import MysticEyesTable from '@components/mysticEyes.vue'
import RollHistory from '@components/rollHistory.vue'
import CharacterInfo from '@components/info.vue'
import CharacterHeader from '@components/characterHeader.vue'
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
import ToastHandler from '@components/toastHandler.vue'
import ToastError from '@scripts/ToastError.ts'
import AppLayout from '@layouts/AppLayout.vue'
import Icon, { type IconName } from '@ui/Icon.vue'
import NumberInputModal from '@modals/numberInputModal.vue'
import { AxiosError } from 'axios'
import type { AxiosResponse } from 'axios'
import type { Sheet } from 'rpgTypes'

interface Props {
    sheet: Sheet
}

const props = defineProps<Props>();

const header = ref<InstanceType<typeof CharacterHeader>>();
const scripture = ref<InstanceType<typeof Scripture>>();

type TabKey = "sheet" | "powers" | "items" | "profile";

const tabs: { key: TabKey; label: string; icon: IconName }[] = [
    { key: "sheet", label: "Ficha", icon: "activity" },
    { key: "powers", label: "Poderes", icon: "sparkles" },
    { key: "items", label: "Itens & Vantagens", icon: "backpack" },
    { key: "profile", label: "Perfil", icon: "book-open" },
];

const activeTab = ref<TabKey>(readStoredTab());
watch(activeTab, (tab: TabKey) => {
    try {
        localStorage.setItem("sheet-tab", tab);
    } catch (e) {
        // Storage can be unavailable (private mode); the tab just won't be remembered.
    }
});

const tabCounts = computed<Partial<Record<TabKey, number>>>(() => ({
    powers: Object.keys(props.sheet.schools).length
        + Object.keys(props.sheet.sonatas).length
        + Object.keys(props.sheet.systems).length
        + props.sheet.miracles.length
        + props.sheet.mysticEyes.length,
    items: props.sheet.items.length + props.sheet.advantages.length,
}));

function readStoredTab() : TabKey {
    try {
        const storedTab = localStorage.getItem("sheet-tab");
        if (tabs.some(tab => tab.key === storedTab)) {
            return storedTab as TabKey;
        }
    } catch (e) {
        // Fall through to the default tab.
    }

    return "sheet";
}

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

function persist() : void {
    if (
        header.value?.points?.remainingPoints == undefined ||
        header.value.points.remainingPoints < 0 ||
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
    window.axios.get("/api/sheets")
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
    <AppLayout>
        <template #actions>
            <button type="button" class="btn btn-ghost btn-sm" title="Rolagem genérica" @click="rollModal()">
                <Icon name="dice" /><span class="hidden md:inline">Rolar</span>
            </button>
            <button type="button" class="btn btn-ghost btn-sm" title="Terminar turno" @click="endTurn()">
                <Icon name="skip" /><span class="hidden md:inline">Terminar turno</span>
            </button>
            <button type="button" class="btn btn-primary btn-sm" title="Salvar ficha" @click="persist()">
                <Icon name="save" /><span class="hidden sm:inline">Salvar</span>
            </button>
        </template>

        <template #aside>
            <RollHistory :sheet />
        </template>

        <div class="flex flex-col gap-6">
            <CharacterHeader :sheet ref="header" />

            <nav role="tablist" aria-label="Seções da ficha" class="-mx-4 flex gap-1 overflow-x-auto border-b border-base-content/10 px-4 sm:mx-0 sm:px-0">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    role="tab"
                    :aria-selected="activeTab === tab.key"
                    class="relative -mb-px flex shrink-0 items-center gap-2 border-b-2 px-3 py-2.5 font-display text-sm font-medium transition-colors"
                    :class="activeTab === tab.key ? 'border-primary text-base-content' : 'border-transparent text-base-content/55 hover:text-base-content'"
                    @click="activeTab = tab.key"
                >
                    <Icon :name="tab.icon" class="size-4" :class="activeTab === tab.key ? 'text-primary' : 'text-base-content/40'" />
                    {{ tab.label }}
                    <span v-if="tabCounts[tab.key]" class="badge badge-xs badge-ghost font-mono">{{ tabCounts[tab.key] }}</span>
                </button>
            </nav>

            <div v-show="activeTab === 'sheet'" role="tabpanel" class="grid items-start gap-4 xl:grid-cols-2">
                <StatsTable :sheet :key="statsKey" />
                <SkillsTable :sheet :key="skillsKey" />
            </div>

            <div v-show="activeTab === 'powers'" role="tabpanel" class="grid items-start gap-4 xl:grid-cols-2">
                <SchoolsTable :sheet @add="schoolsModal?.modalRef?.showModal()" @sync="updateSheet()" v-if="sheet.classes['isMage']" :key="schoolsKey"/>
                <SonatasTable :sheet @add="sonatasModal?.modalRef?.showModal()" @addAbility="(sonataId: string) => sonataAbilitiesModal?.build(sonataId)" v-if="sheet.classes['isVampire']" :key="sonatasKey" />
                <Scripture :sheet :key="scriptureKey" v-if="sheet.classes['isCleric']" @add="scriptureAbilitiesModal?.modalRef?.showModal()" ref="scripture" />
                <Systems :sheet :key="systemsKey" v-if="sheet.classes['isMagiteck']" @add="systemsModal?.modalRef?.showModal()" @addSubsystem="(systemId: string) => subsystemsModal?.build(systemId)" />
                <MiraclesTable :sheet @add="miraclesModal?.modalRef?.showModal()" v-if="sheet.classes['isCleric']" :key="miraclesKey" />
                <MysticEyesTable :sheet @add="eyesModal?.modalRef?.showModal()" @target="targetModal?.updateCharacters(); targetModal?.modalRef?.showModal()" :key="mysticEyesKey" ref="mysticEyesTable" />
            </div>

            <div v-show="activeTab === 'items'" role="tabpanel" class="grid items-start gap-4 xl:grid-cols-2">
                <ItemsTable :sheet @sync="updateSheet()" />
                <Advantages :sheet @add="advantagesModal?.modalRef?.showModal()" :key="advantagesKey" />
            </div>

            <div v-show="activeTab === 'profile'" role="tabpanel">
                <CharacterInfo :sheet />
            </div>
        </div>
    </AppLayout>

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
