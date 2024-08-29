<script setup lang="ts">
import { ref, watch } from "vue";
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

function increase() : void {
    props.sheet.creationPoints++;
}

function increaseBy5() : void {
    props.sheet.creationPoints += 5;
}

function increaseBy10() : void {
    props.sheet.creationPoints += 10;
}

function decrease() : void {
    props.sheet.creationPoints--;
}

function decreaseBy5() : void {
    if (props.sheet.creationPoints - 5 < originalPoints) {
        props.sheet.creationPoints = originalPoints;
    } else {
        props.sheet.creationPoints -= 5;
    }
}

function decreaseBy10() : void {
    if (props.sheet.creationPoints - 10 < originalPoints) {
        props.sheet.creationPoints = originalPoints;
    } else {
        props.sheet.creationPoints -= 10;
    }
}

function canDecrease() : boolean {
    return props.sheet.creationPoints > originalPoints;
}

</script>

<template>
    <div class="flex flex-row">
        <div>
            <p>Restantes: {{ remainingPoints }}</p>
            <p>Total: {{ sheet.creationPoints }}</p>
        </div>
        <div class="flex flex-row flex-grow my-auto gap-1 justify-end">
            <button class="btn btn-outline btn-primary btn-square btn-xs" @click.exact="increase()" @click.shift.exact="increaseBy10()" @click.alt.exact="increaseBy5">+</button>
            <button v-show="canDecrease()" class="btn btn-outline btn-accent btn-square btn-xs" @click.exact="decrease()" @click.shift.exact="decreaseBy10()" @click.alt.exact="decreaseBy5">-</button>
            <div v-show="!canDecrease()" class="btn-xs btn-square"></div>
        </div>
    </div>
</template>
