<script setup lang="ts">
import { ref, onBeforeMount, Ref } from "vue"
import type { Sheet, Miracle } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props: Props = defineProps<Props>();

const modalRef: Ref<HTMLDialogElement | null> = ref(null);
const miracles: Ref<Miracle[] | null>  = ref(null);

onBeforeMount(() => { getMiracles() })

defineExpose({ modalRef });

async function getMiracles() : Promise<void> {
    const url: string = "/api/miracles";
    try {
        const response: Response = await fetch(url);
        if (!response.ok) {
            throw new Error(await response.text());
        }

        miracles.value = JSON.parse(await response.text())["data"];
    } catch (error) {
        let open: Window | null = window.open();

        if (open != null) {
            open.document.body.innerHTML = error.message;
        }
    }
}

function addToSheet(index: number) : void {
    if (miracles.value == null) {
        return;
    }

    let toAdd: Miracle = miracles.value[index];
    let original: Miracle[] = props.sheet.miracles;

    let shouldAdd: boolean = true;
    original.forEach(
        (value) => {
            if (value.name == toAdd.name) {
                shouldAdd = false;
            }
        }
    );

    if (shouldAdd) {
        props.sheet.miracles.push(toAdd);
    }
}

</script>

<template>
    <dialog id="miracles_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Milagres</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="miracle, key in miracles">
                    <p class="text-2xl">{{ miracle.name }}</p>
                    <p class="text-md"> {{ miracle.description }}</p>
                    <p class="text-md">Custo: {{ miracle.cost }}</p>
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
