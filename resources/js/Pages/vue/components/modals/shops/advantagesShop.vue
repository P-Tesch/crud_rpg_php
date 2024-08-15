<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import type { Sheet, Advantage } from "rpgTypes";
import type { Ref } from "vue";

interface Props {
    sheet: Sheet;
}

const props: Props = defineProps<Props>();

const modalRef: Ref<HTMLDialogElement | null> = ref(null);
const advantages: Ref<Advantage[] | null> = ref(null);

onBeforeMount(() => { getAdvantages() });

defineExpose({ modalRef });

async function getAdvantages() : Promise<void> {
    const url: string = "/api/advantages";
    try {
        const response: Response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        advantages.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        let open: Window | null = window.open();

        if (open != null) {
            open.document.body.innerHTML = error.message;
        }
    }
}

function addToSheet(index: number) : void {
    if (advantages.value == null) {
        return;
    }

    let toAdd: Advantage = advantages.value[index];
    let original: Advantage[] = props.sheet.advantages;
    let shouldAdd: boolean = true;

    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                if (value.level >= toAdd.level) {
                    shouldAdd = false;
                } else {
                    props.sheet.advantages.splice(props.sheet.advantages.indexOf(value), 1);
                }
            }
        }
    );

    if (shouldAdd) {
        props.sheet.advantages.push(toAdd);
    }
}

</script>

<template>
    <dialog id="eyes_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Vantagens</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="advantage, key in advantages">
                    <p class="text-2xl">{{ advantage.name }}</p>
                    <p class="text-md"> {{ advantage.description }}</p>
                    <p class="text-md">Level: {{ advantage.level }}</p>
                    <p class="text-md">Custo: {{ advantage.cost }}</p>
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
