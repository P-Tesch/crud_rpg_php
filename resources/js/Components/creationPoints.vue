<script setup lang="ts">
import { ref, watch } from "vue";
import Stepper from "@ui/Stepper.vue";
import type { Advantage, Miracle, MysticEye, School, Sheet } from "rpgTypes";

interface Props {
    sheet: Sheet;
}

const props = defineProps<Props>();

const originalPoints: number = props.sheet.creationPoints;

const remainingPoints = ref<number>(0);
calculatePoints();
watch(props.sheet, () => calculatePoints());

defineExpose({remainingPoints});

function calculatePoints() : void {
    remainingPoints.value
        = props.sheet.creationPoints
            - totalSkillsCost()
            - totalStatsCost()
            - totalSchoolsCost()
            - totalMysticEyesCost()
            - totalAdvantagesCost()
            - totalMiraclesCost()
            - totalScriptureCost();
}

function totalStatsCost() : number {
    return Object
        .values(props.sheet.stats)
        .reduce(
            function (total: number, item: number, index: number) {
                let sum: number;
                switch (Object.keys(props.sheet.stats)[index]) {
                    case "tech":
                    case "lineage":
                    case "blood":
                    case "beast":
                        sum = 4 * summation(item) - 4;
                        break;
                    default:
                        sum = 2 * summation(item) - 2
                }
                return sum >= 0 ? total + sum : total;
            },
            0
        );
}

function totalSkillsCost() : number {
    return Object
        .values(props.sheet.skills)
        .reduce(
            function (total: number, skillValue: number) {
                return total + summation(skillValue)
            },
            0
        );
}

function totalSchoolsCost() : number {
    return Object
        .values(props.sheet.schools)
        .reduce(
            function (total: number, school: School) {
                return total + school.cost;
            },
            0
        );
}

function totalMiraclesCost() : number {
    return Object
        .values(props.sheet.miracles)
        .reduce(
            function (total: number, miracle: Miracle) {
                return total + miracle.cost
            },
            0
        );
}

function totalMysticEyesCost() : number {
    return Object
        .values(props.sheet.mysticEyes)
        .reduce(
            function (total: number, eye: MysticEye) {
                return total + eye.cost
            },
            0
        );
}

function totalAdvantagesCost() : number {
    return Object
        .values(props.sheet.advantages)
        .reduce(
            function (total: number, advantage: Advantage) {
                return total + advantage.cost
            },
            0
        );
}

function totalScriptureCost() : number {
    if (props.sheet.scripture == null) {
        return 0;
    }
    return summation(props.sheet.scripture.creation_points / 50) * 4;
}

function summation(number: number) : number {
    let total: number = 0;
    for (let i = 1; i <= number; i++) {
        total += i;
    }
    return total;
}

function increase(event: MouseEvent) : void {
    if (event.shiftKey) {
        props.sheet.creationPoints += 10;
        return;
    }

    if (event.altKey) {
        props.sheet.creationPoints += 5;
        return;
    }

    props.sheet.creationPoints++;
}

function decrease(event: MouseEvent) : void {
    const amount: number = event.shiftKey ? 10 : (event.altKey ? 5 : 1);

    if (props.sheet.creationPoints - amount < originalPoints) {
        props.sheet.creationPoints = originalPoints;
        return;
    }

    props.sheet.creationPoints -= amount;
}

function canDecrease() : boolean {
    return props.sheet.creationPoints > originalPoints;
}

</script>

<template>
    <div class="flex items-end gap-4">
        <div>
            <p class="label-mono">Pontos de criação</p>
            <p class="mt-1 font-mono leading-none tabular-nums">
                <span class="text-3xl font-semibold" :class="remainingPoints < 0 ? 'text-error' : 'text-base-content'">{{ remainingPoints }}</span>
                <span class="text-sm text-base-content/45"> / {{ sheet.creationPoints }}</span>
            </p>
        </div>
        <Stepper
            class="mb-0.5"
            label="pontos de criação"
            :can-decrease="canDecrease()"
            title="Shift: ±10 · Alt: ±5"
            @increase="increase"
            @decrease="decrease"
        />
    </div>
</template>
