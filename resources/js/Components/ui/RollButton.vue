<script setup lang="ts">
import Icon from "@ui/Icon.vue";
</script>

<template>
    <button type="button" class="btn btn-xs btn-soft btn-secondary gap-1.5">
        <Icon name="dice" class="size-3.5" />
        <slot>Rolar</slot>
    </button>
</template>
