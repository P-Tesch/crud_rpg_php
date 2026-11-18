<script setup lang="ts">
import { computed, inject } from "vue";
import GlossaryTerm from "@ui/GlossaryTerm.vue";
import { glossaryChainKey, maxGlossaryDepth, normalizeEffectName, parseGlossaryMarkup, useGlossary } from "@scripts/glossary.ts";
import type { GlossaryEffect } from "rpgTypes";

interface Props {
    text: string | null | undefined;
}

const props = defineProps<Props>();

const chain = inject(glossaryChainKey, null);
const { findEffect } = useGlossary();

const segments = computed(() => parseGlossaryMarkup(props.text ?? ""));

/** Unknown effects, effects already open higher up the chain and terms past the depth cap stay plain. */
function linkableEffect(name: string) : GlossaryEffect | undefined {
    if (chain != null && (chain.depth + 1 >= maxGlossaryDepth || chain.names.includes(normalizeEffectName(name)))) {
        return undefined;
    }

    return findEffect(name);
}
</script>

<!-- Renders game text with [[Effect]] / [[Effect|text]] glossary markup turned into hoverable terms. -->
<template>
    <p>
        <template v-for="(segment, index) in segments" :key="index">
            <GlossaryTerm
                v-if="segment.kind === 'term' && linkableEffect(segment.name)"
                :effect="linkableEffect(segment.name)!"
                :label="segment.text"
            />
            <template v-else>{{ segment.text }}</template>
        </template>
    </p>
</template>
