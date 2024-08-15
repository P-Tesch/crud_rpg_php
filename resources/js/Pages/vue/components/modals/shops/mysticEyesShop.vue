<script setup lang="ts">
import { ref, Ref, onBeforeMount } from "vue"
import type { Sheet, MysticEye } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props: Props = defineProps<Props>();
const modalRef: Ref<HTMLDialogElement | null> = ref(null);
const eyes: Ref<MysticEye[] | null> = ref(null);

onBeforeMount(() => { getEyes() })

defineExpose({ modalRef });

async function getEyes() : Promise<void> {
        const url: string = "/api/mystic_eyes";
    try {
        const response: Response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        eyes.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        let open: Window | null = window.open();

        if (open != null) {
            open.document.body.innerHTML = error.message;
        }
    }
}

function addToSheet(index: number) : void {
    if (eyes.value == null) {
        return;
    }

    let toAdd: MysticEye = eyes.value[index];
    let original: MysticEye[] = props.sheet.mysticEyes;
    if (original.length >= 2) {
        return;
    }

    let exists: boolean = false;
    original.forEach(eye => {
        if (eye.name == toAdd.name) {
            exists = true;
        }
    });

    if (exists) {
        return;
    }

    toAdd.pivot = {
        "current_cooldown": toAdd.cooldown,
        "sheet_id": props.sheet.id,
        "mystic_eye_id": toAdd.id
    };

    props.sheet.mysticEyes.push(toAdd);
}

</script>

<template>
    <dialog id="eyes_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Olhos místicos</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="value, key in eyes">
                    <div class="collapse collapse-arrow bg-base-100">
                        <input type="checkbox" name="mystic-eyes-collapse" />
                        <div class="collapse-title text-xl font-medium">{{ value.name }}</div>
                        <div class="collapse-content">
                            <div v-if="value.passive != null" class="collapse collapse-arrow bg-base-100">
                                <input type="checkbox" name="me-passive-collapse" />
                                <div class="collapse-title text-xl font-medium">Passivo</div>
                                <div class="collapse-content">
                                    <p>{{ value.passive }}</p>
                                </div>
                            </div>
                            <div v-if="value.active != null" class="collapse collapse-arrow bg-base-100">
                                <input type="checkbox" name="me-passive-collapse" />
                                <div class="collapse-title text-xl font-medium">Ativo</div>
                                <div class="collapse-content grid grid-flow-row grid-cols-2">
                                    <p class="col-start-1 row-start-1">{{ value.active }}</p>
                                    <p class="col-start-1 row-start-2">Cooldown: {{ value.cooldown }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="p-5">Custo: {{ value.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end"
                        @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
