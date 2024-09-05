<script setup lang="ts">
import { ref } from "vue";
import { AxiosError } from "axios";
import ToastError from "@scripts/ToastError.ts";
import type { Sheet, SonataAbility } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const modalRef = ref<HTMLDialogElement>();
const sonataAbilities = ref<SonataAbility[]>([]);
const sonataNameRef = ref<string>("");

defineExpose({modalRef, build});

function build(sonataName: string) {
    window.axios.get("/api/sonata_abilities", {
        params: {
            sonata_name: sonataName
        }
    }).then((response: AxiosResponse) => {
            sonataNameRef.value = sonataName;
            sonataAbilities.value = response.data["data"];
            modalRef.value?.showModal();
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar habilidades de sonata", error);
    }
);
}

function addToSheet(index: number) : void {
    const ability: SonataAbility = sonataAbilities.value[index];

    props.sheet.sonatas[sonataNameRef.value].abilities.forEach((entry, key) => {
        if (entry.name == ability.name) {
            if (entry.level >= ability.level) {
                throw new ToastError("O personagem já possui essa habilidade em um nível igual ou maior");
            }

            delete props.sheet.sonatas[sonataNameRef.value].abilities[key];
        }
    });

    props.sheet.sonatas[sonataNameRef.value].abilities.push(ability);
}

</script>

<template>
    <dialog id="sonata_abilities_modal" class="modal" ref="modalRef">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-3xl font-bold text-center">Habilidades de sonata</h3>
            <div class="flex flex-col gap-5">
                <div class="flex flex-col outline outline-primary p-2 rounded-box" v-for="ability, key in sonataAbilities">
                    <h4 class="text-xl font-semibold">{{ ability.name }}</h4>
                    <p>{{ ability.description }}</p>
                    <p>Level: {{ ability.level }}</p>
                    <p>Custo: {{ ability.cost }}</p>
                    <button class="btn btn-outline btn-accent btn-md self-end" @click="addToSheet(key)">Adicionar</button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</template>
