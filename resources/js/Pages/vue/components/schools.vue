<script setup lang="ts">
import { ref, toRaw } from "vue";
import FailToast from './alerts/failToast.vue';
import NumberInputModal from './modals/numberInputModal.vue';
import { School, SchoolArray, Sheet } from 'rpgTypes';

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalSchools: SchoolArray | any[] = structuredClone(toRaw(props.sheet.schools));

const emit = defineEmits(["sync", "add"]);
const failToast = ref<InstanceType<typeof FailToast>>();
const costModal = ref<InstanceType<typeof NumberInputModal>>();

const types = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    "null": "Outro"
}

function rollSpell(school: string | number, spell: string | number) : void {
    this.costModal.modalRef.showModal();
    this.school = school;
    this.spell = spell;
}

async function roll(cost: number) : Promise<void> {
    if (props.sheet.attributes.mana < cost || cost == null) {
        this.failToast.toastRef = true;
        setTimeout(() => this.failToast.toastRef = false, 2500);
        return;
    }

    const url: string = "/api/roll/spell?school=" + this.school + "&spell=" + this.spell + "&cost=" + cost + "&modifier=0";
    try {
        const response: Response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        emit("sync");

    } catch (error) {
        let open: Window | null = window.open();

        if (open != null) {
            open.document.body.innerHTML = error.message;
        }
    }
}

function isOriginal(value: School, key: string | number) : boolean {
    let isOriginal: boolean = false;
    if (originalSchools.hasOwnProperty(key)) {
        isOriginal = originalSchools[key].level >= value.level;
    }

    return !isOriginal;
}

function remove(key: string | number) : void {
    let isPresent: boolean = originalSchools.hasOwnProperty(key);

    if (!isPresent) {
        delete props.sheet.schools[key];
    } else {
        props.sheet.schools[key] = structuredClone(originalSchools[key]);
    }
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 flex flex-col justify-between">
        <div>
            <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
                <h1 class="font-semibold text-2xl">Escolas</h1>
            </div>
            <div class="overflow-auto">
                <div v-for="school, key in sheet.schools" class="collapse collapse-arrow bg-base-100">
                    <button v-if="isOriginal(school, key)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="remove(key)">✕</button>
                    <input type="checkbox" name="schools-collapse" />
                    <div class="collapse-title text-xl font-medium">{{ key }} [{{ school.level }}]</div>
                    <div class="collapse-content">
                        <div v-for="spell, k in school.spells" class="collapse collapse-arrow bg-base-100">
                            <input type="checkbox" name="spells-collapse" />
                            <div class="collapse-title text-xl font-medium">{{ k }}</div>
                            <div class="collapse-content">
                                <p>Tipo: {{ types[spell.type] }}</p>
                                <p>Descrição: {{ spell.description }}</p>
                                <button class="btn btn-outline btn-secondary btn-sm" @click="rollSpell(key, k)">Rolar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>
    <FailToast class="z-10" ref="failToast" :message="'Mana insuficiente'" />
    <Teleport to="body">
        <NumberInputModal :title="'Insira o custo da magia'" @end="(cost) => roll(cost)" ref="costModal"/>
    </Teleport>
</template>
