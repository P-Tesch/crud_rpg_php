<script setup lang="ts">
import { computed, inject, nextTick, onBeforeUnmount, provide, reactive, ref, useId, watch } from "vue";
import Icon from "@ui/Icon.vue";
import RichText from "@ui/RichText.vue";
import { glossaryChainKey, normalizeEffectName } from "@scripts/glossary.ts";
import type { GlossaryEffect } from "rpgTypes";

interface Props {
    effect: GlossaryEffect;
    label: string;
}

const props = defineProps<Props>();

/** Delay before a hovered term opens, so sweeping the pointer across text doesn't flash tooltips. */
const openDelay = 150;
/**
 * Grace period that lets the pointer travel from a term into its tooltip. Nested tooltips sit beside
 * their parent, farther from the term, so they get longer.
 */
const closeGraceRoot = 250;
const closeGraceNested = 450;
const viewportMargin = 8;
const gap = 6;

const parent = inject(glossaryChainKey, null);
const tooltipId = useId();
const path = parent == null ? tooltipId : `${parent.path}/${tooltipId}`;
const depth = parent == null ? 0 : parent.depth + 1;

const term = ref<HTMLElement>();
const tooltip = ref<HTMLElement>();
const teleportTarget = ref<HTMLElement | string>("body");
const position = ref<{ top: number; left: number }>({ top: 0, left: 0 });
const isPlaced = ref<boolean>(false);

const isOpen = ref<boolean>(false);
const isPinned = ref<boolean>(false);
const isHoveringTerm = ref<boolean>(false);
const isHoveringTooltip = ref<boolean>(false);
const isFocused = ref<boolean>(false);
/** Nested tooltips that are open and in use; they keep this one open even though the pointer left it. */
const holdingChildren = reactive(new Set<string>());
const openChildren = reactive(new Set<string>());

const isEngaged = computed<boolean>(() =>
    isHoveringTerm.value || isHoveringTooltip.value || isFocused.value || holdingChildren.size > 0
);
const isHoldingParent = computed<boolean>(() => isOpen.value && (isEngaged.value || isPinned.value));

provide(glossaryChainKey, {
    depth,
    names: [...(parent?.names ?? []), normalizeEffectName(props.effect.name)],
    path,
    tooltip,
    setChildHolding: (id, isHolding) => isHolding ? holdingChildren.add(id) : holdingChildren.delete(id),
    setChildOpen: (id, isChildOpen) => isChildOpen ? openChildren.add(id) : openChildren.delete(id),
});

let openTimer: ReturnType<typeof setTimeout> | undefined;
let closeTimer: ReturnType<typeof setTimeout> | undefined;

watch(isHoldingParent, (isHolding) => parent?.setChildHolding(tooltipId, isHolding));
watch(isOpen, (open) => parent?.setChildOpen(tooltipId, open));

watch(isEngaged, (engaged) => {
    clearTimeout(closeTimer);

    if (engaged) {
        return;
    }

    clearTimeout(openTimer);

    if (isOpen.value && !isPinned.value) {
        closeTimer = setTimeout(close, depth == 0 ? closeGraceRoot : closeGraceNested);
    }
});

function open() : void {
    clearTimeout(openTimer);
    clearTimeout(closeTimer);

    if (isOpen.value || term.value == undefined) {
        return;
    }

    // Native modals live in the top layer, above anything teleported to <body>.
    teleportTarget.value = term.value.closest("dialog") ?? "body";
    isPlaced.value = false;
    isOpen.value = true;

    window.addEventListener("keydown", closeOnEscape);
    window.addEventListener("scroll", closeOnOutsideScroll, { capture: true });
    document.addEventListener("pointerdown", closeOnOutsidePointer, { capture: true });

    nextTick(place);
}

function close() : void {
    clearTimeout(openTimer);
    clearTimeout(closeTimer);
    isOpen.value = false;
    isPinned.value = false;
    isHoveringTooltip.value = false;

    window.removeEventListener("keydown", closeOnEscape);
    window.removeEventListener("scroll", closeOnOutsideScroll, { capture: true });
    document.removeEventListener("pointerdown", closeOnOutsidePointer, { capture: true });
}

function togglePin() : void {
    if (isPinned.value) {
        close();
        return;
    }

    open();
    isPinned.value = true;
}

function onTermPointerEnter(event: PointerEvent) : void {
    // Touch has no hover: a tap pins through the click handler instead.
    if (event.pointerType != "mouse") {
        return;
    }

    isHoveringTerm.value = true;
    clearTimeout(closeTimer);
    openTimer = setTimeout(open, openDelay);
}

function onTermFocus() : void {
    if (term.value?.matches(":focus-visible")) {
        isFocused.value = true;
        open();
    }
}

/** Is the node inside this tooltip or any tooltip nested in it? */
function isWithinChain(target: EventTarget | null) : boolean {
    if (!(target instanceof Element)) {
        return false;
    }

    const ownerPath = target.closest<HTMLElement>("[data-glossary-path]")?.dataset.glossaryPath;

    return ownerPath != undefined && (ownerPath == path || ownerPath.startsWith(path + "/"));
}

function closeOnOutsidePointer(event: PointerEvent) : void {
    if (term.value?.contains(event.target as Node) || isWithinChain(event.target)) {
        return;
    }

    close();
}

/** The innermost open tooltip closes first; outer ones wait for their children. */
function closeOnEscape(event: KeyboardEvent) : void {
    if (event.key != "Escape" || openChildren.size > 0) {
        return;
    }

    event.preventDefault();
    close();

    if (isFocused.value) {
        term.value?.focus();
    }
}

function closeOnOutsideScroll(event: Event) : void {
    if (!isWithinChain(event.target)) {
        close();
    }
}

/** Nested tooltips sit beside their parent tooltip; the outermost (or a cramped viewport) goes below/above the term. */
function place() : void {
    if (term.value == undefined || tooltip.value == undefined) {
        return;
    }

    const termRect = term.value.getBoundingClientRect();
    const tooltipRect = tooltip.value.getBoundingClientRect();
    const parentRect = parent?.tooltip.value?.getBoundingClientRect();
    const maxLeft = window.innerWidth - tooltipRect.width - viewportMargin;
    const maxTop = window.innerHeight - tooltipRect.height - viewportMargin;
    const clamp = (value: number, max: number) => Math.max(viewportMargin, Math.min(value, max));

    if (parentRect != undefined) {
        const fitsRight = parentRect.right + gap + tooltipRect.width <= window.innerWidth - viewportMargin;
        const fitsLeft = parentRect.left - gap - tooltipRect.width >= viewportMargin;

        if (fitsRight || fitsLeft) {
            position.value = {
                top: clamp(termRect.top - 10, maxTop),
                left: fitsRight ? parentRect.right + gap : parentRect.left - gap - tooltipRect.width,
            };
            isPlaced.value = true;
            return;
        }
    }

    const fitsBelow = termRect.bottom + gap + tooltipRect.height <= window.innerHeight - viewportMargin;

    position.value = {
        top: fitsBelow ? termRect.bottom + gap : clamp(termRect.top - gap - tooltipRect.height, maxTop),
        left: clamp(termRect.left, maxLeft),
    };
    isPlaced.value = true;
}

onBeforeUnmount(() => {
    close();
    parent?.setChildHolding(tooltipId, false);
    parent?.setChildOpen(tooltipId, false);
});
</script>

<template>
    <span
        ref="term"
        role="button"
        tabindex="0"
        class="cursor-help rounded-sm font-medium text-accent transition-colors outline-none focus-visible:outline-1 focus-visible:outline-offset-2 focus-visible:outline-accent"
        :class="{ 'bg-accent/15': isOpen }"
        :aria-expanded="isOpen"
        :aria-describedby="isOpen ? tooltipId : undefined"
        @pointerenter="onTermPointerEnter"
        @pointerleave="isHoveringTerm = false"
        @focus="onTermFocus()"
        @blur="isFocused = false"
        @click.stop="togglePin()"
        @keydown.enter.prevent="togglePin()"
        @keydown.space.prevent="togglePin()"
    >{{ label }}</span>
    <Teleport v-if="isOpen" :to="teleportTarget">
        <div
            ref="tooltip"
            :id="tooltipId"
            role="tooltip"
            :data-glossary-path="path"
            class="fixed w-max max-w-80 rounded-box border bg-base-300/95 px-3 py-2.5 text-left text-xs leading-relaxed font-normal text-base-content normal-case tracking-normal shadow-xl backdrop-blur-md transition-opacity duration-150"
            :class="[isPinned ? 'border-accent/50' : 'border-base-content/15', isPlaced ? 'opacity-100' : 'opacity-0']"
            :style="{ top: `${position.top}px`, left: `${position.left}px`, zIndex: 60 + depth }"
            @pointerenter="isHoveringTooltip = true"
            @pointerleave="isHoveringTooltip = false"
        >
            <div class="mb-1 flex items-center gap-2">
                <span class="font-display text-sm font-semibold text-accent">{{ effect.name }}</span>
                <span class="label-mono">Efeito</span>
                <Icon v-if="isPinned" name="pin" class="ml-auto size-3.5 text-accent" />
            </div>
            <RichText :text="effect.description" class="text-base-content/85" />
        </div>
    </Teleport>
</template>
