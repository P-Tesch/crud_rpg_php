<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"
import FailToast from './alerts/failToast.vue';
import NumberInputModal from './modals/numberInputModal.vue';

const props = defineProps({ sheet: Object })

const originalSchools = Object.assign({}, props.sheet.schools);

const emit = defineEmits(["sync", "add"]);
const failToast = ref(null);
const costModal = ref(null);

const types = {
    "PROJECTILE": "Projétil",
    "DIRECT": "Direto",
    null: "Outro"
}

async function rollSpell(school, spell) {
    this.costModal.modalRef.showModal();
    this.school = school;
    this.spell = spell;
}

async function roll(cost) {
    if (props.sheet.attributes.mana < cost || cost == null) {
        this.failToast.toastRef = true;
        setTimeout(() => this.failToast.toastRef = false, 2500);
        return;
    }

    const url = "/api/roll/spell?school=" + this.school + "&spell=" + this.spell + "&cost=" + cost + "&modifier=0";
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        emit("sync");

    } catch (error) {
        window.open().document.body.innerHTML = error.message;
    }
}

function isOriginal(value) {
    let schoolsArray = Object.values(originalSchools);
    let isOriginal = false;
    schoolsArray.forEach(
        (v) => {
            if (v.id == value.id) {
                isOriginal = true;
            }
        }
    );

    return !isOriginal;
}

</script>

<template>
    <div class="border border-1 rounded-md border-primary px-3 flex flex-col justify-between">
        <div>
            <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
                <h1 class="font-semibold text-2xl">Escolas</h1>
            </div>
            <div class="overflow-auto">
                <div v-for="value, key in sheet.schools" class="collapse collapse-arrow bg-base-100">
                    <button v-if="isOriginal(value)" class="btn btn-sm btn-circle btn-ghost absolute right-10 top-3.5 z-10 overflow-visible" @click="delete sheet.schools[key]">✕</button>
                    <input type="checkbox" name="schools-collapse" />
                    <div class="collapse-title text-xl font-medium">{{ key }} [{{ value.level }}]</div>
                    <div class="collapse-content">
                        <div v-for="v, k in value.spells" class="collapse collapse-arrow bg-base-100">
                            <input type="checkbox" name="spells-collapse" />
                            <div class="collapse-title text-xl font-medium">{{ k }}</div>
                            <div class="collapse-content">
                                <p>Tipo: {{ types[v.type] }}</p>
                                <p>Descrição: {{ v.description }}</p>
                                <button class="btn btn-outline btn-secondary btn-sm" @click="rollSpell(key, k)">Roll</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="this.$emit('add')">Adicionar</button>
        </div>
    </div>
    <FailToast class="z-10" ref="failToast" :message="'Mana insuficiente'" />
    <Teleport to="body">
        <NumberInputModal :title="'Insira o custo da magia'" @end="(cost) => roll(cost)" ref="costModal"/>
    </Teleport>
</template>
