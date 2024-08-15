declare module "rpgTypes" {

    export type Advantage = {
        name: string;
        description: string;
        level: number;
        cost: number;
    }

    export type ScriptureAbilitiesPivot = {
        scripture_ability_id: number;
        scripture_id: number;
    }

    export type ScriptureAbility = {
        cost: number;
        description: string;
        id: number;
        level: number;
        name: string;
        pivot: ScriptureAbilitiesPivot
    }

    export type Scripture = {
        creation_points: number;
        damage: number;
        description: string;
        double: number | boolean;
        id: number;
        name: string;
        range: number;
        remaining_scripture_points: number;
        scriptureAbilities: ScriptureAbility[];
        sharpness: number;
        sheet_id: number;
    }

    export type Miracle = {
        cost: number;
        description: string;
        id: number;
        name: string;
    }

    export type MysticEyesPivot = {
        current_cooldown: number;
        mystic_eye_id: number;
        sheet_id: number;
    }

    export type MysticEye = {
        active: string;
        cooldown: number;
        cost: number;
        id: number;
        name: string;
        passive: string;
        pivot: MysticEyesPivot
    }

    export type Spell = {
        description: string;
        strategy: string | null;
        type: string;
    }

    export interface SpellFromShop extends Spell {
        name: string;
    }

    export type SpellArray = {
        [key: string]: Spell;
    }

    type SchoolBase = {
        id: number;
        level: number;
        cost: number;
    }

    export interface School extends SchoolBase {
        spells: SpellArray;
    }

    export interface SchoolFromShop extends SchoolBase {
        name: string;
        description: string;
        spells: SpellFromShop[];
    }

    export type SchoolArray = {
        [key: string]: School;
    }

    export type Sheet = {
        advantages: Advantage[];
        alignment: string | null;
        attributes: Map<string, number>;
        background: string;
        blood: null;
        classes: Map<string, boolean>;
        creationPoints: number;
        description: string;
        id: number;
        items: null[];
        maxAttributes: Map<string, number>;
        miracles: Miracle[];
        mysticEyes: MysticEye[];
        name: string;
        organization: string | null;
        portrait: string;
        schools: SchoolArray | any[];
        scripture: Scripture;
        skills: Map<string, number>;
        skillsRelations: Map<string, string>;
        sonatas: null[];
        stats: Map<String, number>;
    }
}
