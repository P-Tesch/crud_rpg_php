<script setup lang="ts">
import { ref, onBeforeMount } from "vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { AxiosResponse } from "axios";
import type { Sheet, SonataFromShop } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const sonatas = ref<SonataFromShop[]>([]);

onBeforeMount(() => {getSonatas()})

defineExpose({modalRef});

function getSonatas() : void {
    const url: string = "/api/sonatas";

    window.axios.get(url)
        .then((response: AxiosResponse) => {
            sonatas.value = response.data["data"];
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar sonatas", error);
        }
    );
}

function addToSheet(index: number) : void {
    if (props.sheet.sonatas.length != undefined) {
        props.sheet.sonatas = {};
    }

    const maxSonatas = Math.ceil(props.sheet.stats["lineage"] / 3);
    if (Object.keys(props.sheet.sonatas).length >= maxSonatas) {
        throw new ToastError("O personagem já possui o número máximo de sonatas");
    }

    const sonata = sonatas.value[index];

    if (props.sheet.sonatas.hasOwnProperty(sonata.name)) {
        throw new ToastError("O personagem já possui esssa sonata");
    }

    props.sheet.sonatas[sonata.name] = {
        id: sonata.id,
        abilities: []
    };
}

</script>

<template>
    <dialog id="sonatas_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Sonatas</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="sonata, key in sonatas">
                    <h4 class="text-xl font-semibold">{{ sonata.name }}</h4>
                    <p>{{ sonata.description }}</p>
                    <div v-for="ability in sonata.abilities" class="collapse collapse-arrow bg-base-100">
                        <input type="checkbox" name="sonata-abilities-collapse" />
                        <div class="collapse-title text-xl font-medium">{{ ability.name }}</div>
                        <div class="collapse-content">
                            <p>Descrição: {{ ability.description }}</p>
                            <p>Level: {{ ability.level }}</p>
                            <p>Custo: {{ ability.cost }}</p>
                        </div>
                    </div>
                    <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
