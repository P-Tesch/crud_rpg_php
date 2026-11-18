import { computed, type InjectionKey, type Ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import type { GlossaryEffect } from "rpgTypes";

/** Same syntax as App\Helpers\GlossaryMarkup::TERM_PATTERN: "[[Effect]]" or "[[Effect|displayed text]]". */
const termPattern = /\[\[([^[\]|]+)(?:\|([^[\]]+))?\]\]/g;

/** Deepest tooltip level; terms past it render as plain text. */
export const maxGlossaryDepth = 4;

export type GlossarySegment =
    | { kind: "text"; text: string }
    | { kind: "term"; text: string; name: string };

/** One open tooltip in a nested chain, provided to the terms inside it. */
export type GlossaryChain = {
    depth: number;
    /** Lowercased effect names from the outermost tooltip to this one. */
    names: string[];
    /** "/"-joined tooltip ids from the outermost tooltip to this one, set as data-glossary-path. */
    path: string;
    tooltip: Ref<HTMLElement | undefined>;
    setChildHolding: (id: string, isHolding: boolean) => void;
    setChildOpen: (id: string, isOpen: boolean) => void;
};

export const glossaryChainKey: InjectionKey<GlossaryChain> = Symbol("glossaryChain");

export function normalizeEffectName(name: string) : string {
    return name.trim().toLocaleLowerCase("pt-BR");
}

export function parseGlossaryMarkup(text: string) : GlossarySegment[] {
    const segments: GlossarySegment[] = [];
    let lastIndex = 0;

    for (const match of text.matchAll(termPattern)) {
        if (match.index > lastIndex) {
            segments.push({ kind: "text", text: text.slice(lastIndex, match.index) });
        }

        segments.push({ kind: "term", name: match[1].trim(), text: (match[2] ?? match[1]).trim() });
        lastIndex = match.index + match[0].length;
    }

    if (lastIndex < text.length) {
        segments.push({ kind: "text", text: text.slice(lastIndex) });
    }

    return segments;
}

/** Looks up effects from the "effects" page prop shared by the sheet page. */
export function useGlossary() {
    const page = usePage<{ effects?: GlossaryEffect[] }>();
    const effectsByName = computed(() => new Map(
        (page.props.effects ?? []).map((effect) => [normalizeEffectName(effect.name), effect]),
    ));

    return {
        findEffect: (name: string) : GlossaryEffect | undefined => effectsByName.value.get(normalizeEffectName(name)),
    };
}
