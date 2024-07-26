<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from "vue"

const props = defineProps({ sheet: Object });

const remainingPoints = ref(0);
calculatePoints();
watch(props.sheet, () => calculatePoints());

defineExpose({remainingPoints})

function calculatePoints() {
    remainingPoints.value = props.sheet.creationPoints - totalSkillsCost() - totalStatsCost();
}

function totalStatsCost() {
    return Object
        .values(props.sheet.stats)
        .reduce(
            function (total, item, index) {
                let sum;
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

function totalSkillsCost() {
    return Object
        .values(props.sheet.skills)
        .reduce(
            function (total, item) {
                return total + summation(item)
            },
            0
        );
}

function summation(number) {
    let total = 0;
    for (let i = 1; i <= number; i++) {
        total += i;
    }
    return total;
}

</script>

<template>
    <div>
        <p>Restantes: {{ remainingPoints }}</p>
        <p>Total: {{ sheet.creationPoints }}</p>
    </div>
</template>
