<script setup lang="ts">
import Icon from "@ui/Icon.vue";
</script>

<template>
    <button
        type="button"
        class="btn btn-block btn-sm btn-dash border-base-content/20 font-normal text-base-content/60 hover:border-primary hover:bg-primary/5 hover:text-primary"
    >
        <Icon name="plus" class="size-3.5" />
        <slot>Adicionar</slot>
    </button>
</template>
