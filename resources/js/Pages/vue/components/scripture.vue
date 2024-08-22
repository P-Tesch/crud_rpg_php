<script setup lang="ts">
import { ref, watch } from "vue"
import TextInputModal from './modals/textInputModal.vue'
import TextAreaModal from './modals/textAreaModal.vue'
import { Scripture, Sheet } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const emit = defineEmits(["sync", "add"]);

const yesNo = {
    true: "Sim",
    false: "Não",
    0: "Não",
    1: "Sim"
}

const originalScripture: Scripture = Object.assign({}, props.sheet.scripture);

const nameModal = ref<InstanceType<typeof TextInputModal>>();
const descriptionModal = ref<InstanceType<typeof TextAreaModal>>();

const remainingPoints = ref<number>(0);
calculatePoints();
watch(props.sheet.scripture, () => calculatePoints());

defineExpose({remainingPoints});

function calculatePoints() : void {
    let totalAttributes: number = 0;
    let totalAbilities: number = 0;
    let scripture: Scripture = props.sheet.scripture;

    totalAttributes += scripture.damage * 15;
    totalAttributes += scripture.range * 5;
    totalAttributes += scripture.sharpness * 20;
    totalAttributes += Number(scripture.double) * 30;

    props.sheet.scripture.scriptureAbilities.forEach(ability => {
        totalAbilities += ability.cost;
    });

    remainingPoints.value = props.sheet.scripture.creation_points - totalAbilities - totalAttributes;
}

function editName(name: string) : void {
    if (name != null) {
        props.sheet.scripture.name = name;
    }
}

function editDescription(description: string) : void {
    if (description != null) {
        props.sheet.scripture.description = description;
    }
}

function increase(key: string) : void {
    props.sheet.scripture[key]++;
}

function decrease(key: string) : void {
    props.sheet.scripture[key]--;
}

function canDecrease(key: string) : boolean {
    return originalScripture[key] < props.sheet.scripture[key];
}

function invertDouble() : void {
    props.sheet.scripture.double = !props.sheet.scripture.double;
}

function rollScriptureAbility(scriptureAbility) {
    console.log("TODO");
}

function showModal(modal: InstanceType<typeof TextInputModal | typeof TextAreaModal> | undefined, defaultValue: string) : void {
    modal.modalRef.showModal();
    modal.input = defaultValue;
}

</script>


<template>
    <div class="overflow-x-auto border border-1 rounded-md border-primary px-3 justify-between flex flex-col">
        <div>
            <div class="border border-1 rounded-md border-primary border-t-0 border-x-0 text-center -mx-3">
                <h1 class="font-semibold text-2xl">Escritura</h1>
            </div>

            <div class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" name="info-collapse" />
                <div class="collapse-title text-md font-medium flex gap-5">{{ sheet.scripture.name }}
                    <svg class="stroke-neutral-content h-5 w-5 z-10" @click="showModal(nameModal, sheet.scripture.name)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                    </svg>
                </div>
                <div class="collapse-content overflow-auto">
                    <p>{{ sheet.scripture.description }}</p>
                    <svg class="stroke-neutral-content h-5 w-5 z-10" @click="showModal(descriptionModal, sheet.scripture.description)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                        <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                    </svg>
                </div>
            </div>

            <div class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" name="info-collapse" />
                <div class="collapse-title text-md font-medium flex gap-5">Atributos</div>
                <div class="collapse-content overflow-auto flex flex-col gap-3">

                    <div class="w-full flex flex-row">
                        <span>Pontos de criação: {{ remainingPoints }} / {{ sheet.scripture.creation_points }}</span>
                        <div class="flex flex-row gap-1 justify-end flex-grow">
                            <button class="btn btn-outline btn-primary btn-xs" v-show="sheet.scripture.creation_points < sheet.stats.faith * 50" @click="sheet.scripture.creation_points += 50">+</button>
                            <button class="btn btn-outline btn-accent btn-xs" v-show="canDecrease('creation_points')" @click="sheet.scripture.creation_points -= 50">-</button>
                            <div v-show="!canDecrease('creation_points')" class="btn-xs btn-square"></div>
                        </div>
                    </div>

                    <div class="w-full flex flex-row">
                        <span>Dano: {{ sheet.scripture.damage }}</span>
                        <div class="flex flex-row gap-1 justify-end flex-grow">
                            <button class="btn btn-outline btn-primary btn-xs" v-if="sheet.scripture.damage < 5" @click="increase('damage')">+</button>
                            <button class="btn btn-outline btn-accent btn-xs" v-if="canDecrease('damage')" @click="decrease('damage')">-</button>
                            <div v-show="!canDecrease('damage')" class="btn-xs btn-square"></div>
                        </div>
                    </div>

                    <div class="w-full flex flex-row">
                        <span>Alcance: {{ sheet.scripture.range }}</span>
                        <div class="flex flex-row gap-1 justify-end flex-grow">
                            <button class="btn btn-outline btn-primary btn-xs" v-if="sheet.scripture.range < 15" @click="increase('range')">+</button>
                            <button class="btn btn-outline btn-accent btn-xs" v-if="canDecrease('range')" @click="decrease('range')">-</button>
                            <div v-show="!canDecrease('range')" class="btn-xs btn-square"></div>
                        </div>
                    </div>

                    <div class="w-full flex flex-row">
                        <span>Afiação: {{ sheet.scripture.sharpness }}</span>
                        <div class="flex flex-row gap-1 justify-end flex-grow">
                            <button class="btn btn-outline btn-primary btn-xs" v-if="sheet.scripture.sharpness < 5" @click="increase('sharpness')">+</button>
                            <button class="btn btn-outline btn-accent btn-xs" v-if="canDecrease('sharpness')" @click="decrease('sharpness')">-</button>
                            <div v-show="!canDecrease('sharpness')" class="btn-xs btn-square"></div>
                        </div>
                    </div>

                    <div class="w-full flex flex-row">
                        <span>Dupla: {{ yesNo[Number(sheet.scripture.double)] }}</span>
                        <div class="flex flex-row gap-1 justify-end flex-grow">
                            <input id="checkbox-double" type="checkbox" class="checkbox checkbox-primary" v-if="!originalScripture.double" @click="invertDouble()"></input>
                            <input id="checkbox-double-disabled" type="checkbox" disabled class="checkbox checkbox-primary" v-if="originalScripture.double"></input>
                            <div class="btn-xs btn-square"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-for="ability in sheet.scripture.scriptureAbilities" class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" name="info-collapse">
                <div class="collapse-title text-md font-medium flex gap-t">{{ ability.name }}</div>
                <div class="collapse-content overflow-auto flex flex-col gap-3">
                    <p>{{ ability.description }}</p>
                    <div class="flex flex-row">
                        <span>Level: {{ ability.level }}</span>
                        <button class="btn btn-outline btn-secondary btn-sm ml-auto mr-0" @click="rollScriptureAbility(ability)">Rolar</button>
                    </div>
                </div>
            </div>

        </div>
        <div class="w-full text-center">
            <button class="btn btn-outline btn-accent w-full my-3" @click="$emit('add')">Adicionar</button>
        </div>
    </div>

    <Teleport to="body">
        <TextInputModal :title="'Insira o nome da escritura'" @end="(name: string) => editName(name)" ref="nameModal"/>
    </Teleport>
    <Teleport to="body">
        <TextAreaModal :title="'Insira a descrição da escritura'" @end="(description: string) => editDescription(description)" ref="descriptionModal"/>
    </Teleport>
</template>
