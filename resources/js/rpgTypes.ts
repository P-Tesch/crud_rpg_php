declare module "rpgTypes" {

    export type Advantage = {
        name: string;
        description: string;
        level: number;
        cost: number;
    }

    export type Attributes = {
        [key: string]: number;
    }

    export type Classes = {
        isMage: boolean;
        isCleric: boolean;
        isMagiteck: boolean;
        isVampire: boolean;
        isHybrid: boolean;
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
        [key: string]: number | string | boolean | ScriptureAbility[];
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

    export type Skills = {
        [key: string]: number;
    }

    export type SkillsRelations = {
        [key: string]: string;
    }

    export type Stats = {
        [key: string]: number;
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

    export type SonataAbility = {
        name: string;
        description: string;
        level: number;
        cost: number;
    }

    export type Sonata = {
        id: number;
        abilities: SonataAbility[];
    }

    export type SonataArray = {
        [key: string]: Sonata;
    }

    export interface SonataFromShop extends Sonata {
        name: string;
        description: string;
    }

    export type Subsystem = {
        name: string;
        description: string;
    }

    export type System = {
        id: number;
        subsystems: Subsystem[];
    }

    export type SystemFromShop = {
        id: number;
        name: string;
        description: string;
        damage: number;
        subsystems: Subsystem[];
    }

    export type SystemArray = {
        [key: string]: System;
    }

    export type Item = {
        name: string;
        description: string;
        damage: string;
        id: number;
    }

    export type Sheet = {
        advantages: Advantage[];
        alignment: string | null;
        attributes: Attributes;
        background: string;
        blood: null;
        classes: Classes;
        creationPoints: number;
        description: string;
        id: number;
        items: Item[];
        maxAttributes: Attributes;
        miracles: Miracle[];
        mysticEyes: MysticEye[];
        name: string;
        organization: string | null;
        portrait: string;
        schools: SchoolArray;
        scripture: Scripture;
        skills: Skills;
        skillsRelations: SkillsRelations;
        sonatas: SonataArray;
        stats: Stats;
        systems: SystemArray;
    }

    export type Character = {
        name: string;
        portrait: string;
        sheet_id: number;
        timestamp: number;
    }

    export type RollInternal = {
        rolls: number[];
        hits: number;
    }

    export type Roll = {
        skill: RollInternal | null
        recoil: RollInternal | null;
        success: RollInternal | null;
        specific: RollInternal | null;
        mystic_eye: RollInternal | null;
    }

    export type RollAssociative = {
        portrait: string;
        type: string;
        id: number;
        name: string;
        subject: string;
        target: string | null;
        recoilDamage: number | null;
        cost: number | null;
        rolls: Roll;
    }

    export type AssociativeArray = {
        [key: string]: string
    }
}
