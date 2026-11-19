<script setup lang="ts">
import { AxiosError } from "axios";
import { ref, watch, onMounted } from "vue";
import ToastError from "@scripts/ToastError.ts";
import Icon from "@ui/Icon.vue";
import DieFace from "@ui/DieFace.vue";
import type { RollAssociative, RollInternal, Sheet, AssociativeArray } from "rpgTypes";
import type { AxiosResponse } from "axios";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();
const rolls = ref<RollAssociative[]>([]);

type RollPart = {
    label: string | null;
    result: RollInternal | null | undefined;
    note: string | null;
};

const historyList = ref<HTMLDivElement>();
watch(rolls, () => { scrollToBottom() }, { flush: "post" });

onMounted(() => {
    setupRolls();
});

const skills: AssociativeArray = {
    "speed": "Velocidade",
    "acrobatics": "Acrobacia",
    "melee": "Combate corpo a corpo",
    "intimidation": "Intimidação",
    "ranged": "Combate à distância",
    "stealth": "Furtividade",
    "conscience": "Consciência",
    "investigation": "Investigação",
    "wisdom": "Sabedoria",
    "knowledge": "Conhecimento",
    "medicine": "Medicina",
    "survival": "Sobrevivência",
    "tenacity": "Tenacidade",
    "diplomacy": "Diplomacia",
    "insight": "Perspicácia"
}

defineExpose({rolls});

window.Echo.channel("rolls").listen(".rollsTableUpdated", (echo: RollAssociative[]) => {
    rolls.value = echo;
});

function isOwnRoll(roll: RollAssociative) : boolean {
    return roll["id"] == props.sheet.id;
}

const timeFormatter = new Intl.DateTimeFormat("pt-BR", { hour: "2-digit", minute: "2-digit" });
const dateTimeFormatter = new Intl.DateTimeFormat("pt-BR", { dateStyle: "short", timeStyle: "medium" });

function isHit(die: number) : boolean {
    return die >= 5;
}

function describe(roll: RollAssociative) : string {
    switch (roll.type) {
        case "skill":
            return `rolou ${skills[roll.subject] ?? roll.subject}`;
        case "spell":
            return `rolou ${roll.subject} com custo ${roll.cost}`;
        case "mystic_eye":
            return `rolou ${roll.subject} alvejando ${roll.target}`;
        case "generic":
            return roll.amount != null ? `rolou ${roll.amount} dados` : "rolou dados";
        default:
            return "rolou dados";
    }
}

function parts(roll: RollAssociative) : RollPart[] {
    switch (roll.type) {
        case "skill":
            return [{ label: null, result: roll.rolls.skill, note: null }];
        case "spell":
            return [
                { label: "Coice", result: roll.rolls.recoil, note: `Dano recebido: ${roll.recoilDamage}` },
                { label: "Sucesso", result: roll.rolls.success, note: null },
                { label: "Específico", result: roll.rolls.specific, note: null },
            ].filter((part: RollPart, index: number) => index === 0 || part.result != null);
        case "mystic_eye":
            return [{ label: null, result: roll.rolls.mystic_eye, note: null }];
        case "generic":
            return [{ label: null, result: roll.rolls?.success, note: null }];
        default:
            return [];
    }
}

function setupRolls() : void {
    window.axios.get("/api/roll")
        .then((response: AxiosResponse) => {
            rolls.value = response.data;
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao buscar histórico de rolagens", error);
        }
    );
}

function scrollToBottom() : void {
    if (historyList.value != undefined) {
        historyList.value.scrollTop = historyList.value.scrollHeight;
    }
}

</script>

<template>
    <div id="rollHistoryChat" class="flex h-full flex-col">
        <header class="flex h-16 shrink-0 items-center gap-2.5 border-b border-base-content/10 px-4">
            <Icon name="history" class="text-primary" />
            <h2 class="font-display text-[0.95rem] font-semibold tracking-tight">Rolagens</h2>
            <span class="label-mono ml-auto flex items-center gap-1.5 text-[0.6rem]">
                <span class="relative flex size-1.5">
                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-success opacity-60"></span>
                    <span class="relative inline-flex size-1.5 rounded-full bg-success"></span>
                </span>
                Ao vivo
            </span>
        </header>

        <div ref="historyList" class="flex flex-1 flex-col gap-2 overflow-y-auto p-3">
            <div v-if="rolls.length === 0" class="m-auto flex flex-col items-center gap-2 px-6 text-center text-sm text-base-content/45">
                <Icon name="dice" class="size-8 text-base-content/25" />
                Nenhuma rolagem ainda.
            </div>

            <article
                v-for="roll, index in rolls"
                :key="index"
                class="rounded-field border p-3"
                :class="isOwnRoll(roll) ? 'border-primary/25 border-l-2 border-l-primary bg-primary/6' : 'border-base-content/8 bg-base-100/60'"
            >
                <div class="flex items-center gap-2.5">
                    <div class="size-8 shrink-0 overflow-hidden rounded-full bg-base-300 ring-1 ring-base-content/10">
                        <img v-if="roll.portrait" :src="roll.portrait" alt="" class="size-full object-cover" />
                    </div>
                    <div class="min-w-0 flex-1 leading-tight">
                        <p class="flex items-center gap-1.5 text-sm font-medium">
                            <span class="truncate">{{ roll.name }}</span>
                            <span v-if="isOwnRoll(roll)" class="badge badge-xs badge-soft badge-primary shrink-0">Você</span>
                        </p>
                        <p class="truncate text-xs text-base-content/55" :title="describe(roll)">{{ describe(roll) }}</p>
                    </div>
                    <time
                        v-if="roll.timestamp"
                        :datetime="new Date(roll.timestamp).toISOString()"
                        :title="dateTimeFormatter.format(roll.timestamp)"
                        class="shrink-0 self-start font-mono text-[0.65rem] text-base-content/45 tabular-nums"
                    >{{ timeFormatter.format(roll.timestamp) }}</time>
                </div>

                <div v-for="part, partIndex in parts(roll)" :key="partIndex" class="mt-2.5 flex items-end gap-3">
                    <div class="min-w-0 flex-1">
                        <p v-if="part.label" class="label-mono mb-1 text-[0.6rem]">{{ part.label }}</p>
                        <div class="flex flex-wrap gap-1">
                            <DieFace
                                v-for="die, dieIndex in part.result?.rolls"
                                :key="dieIndex"
                                :value="die"
                                :tone="isHit(die) ? (isOwnRoll(roll) ? 'primary' : 'secondary') : 'muted'"
                            />
                        </div>
                        <p v-if="part.note" class="mt-1 text-xs text-base-content/55">{{ part.note }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="font-mono text-2xl leading-none font-semibold tabular-nums" :class="isOwnRoll(roll) ? 'text-primary' : 'text-secondary'">
                            {{ part.result?.hits ?? '–' }}
                        </p>
                        <p class="label-mono mt-1 text-[0.55rem]">acertos</p>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>
