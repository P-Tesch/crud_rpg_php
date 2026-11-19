import type { Attributes, Sheet } from "rpgTypes";

/**
 * Client-side mirror of SheetEntity::calculateMaxAttributes() (app/Entities/SheetEntity.php),
 * so maximums follow stat/skill changes before the sheet is saved.
 * Keep both in sync when the formulas change.
 */
export function calculateMaxAttributes(sheet: Sheet) : Attributes {
    const stats = sheet.stats;
    const attributes = sheet.attributes;
    const maxAttributes: Attributes = {};

    maxAttributes["max_health"] = 2 + 2 * stats["endurance"] + stats["strength"];
    maxAttributes["max_initiative"] = stats["agility"] + stats["perception"];
    maxAttributes["max_movement"] = 2 + sheet.skills["speed"];

    if ("mana" in attributes) {
        maxAttributes["max_mana"] = 20 * stats["magic"];
    }

    if ("energy" in attributes) {
        maxAttributes["max_energy"] = 20 * stats["blood"];
    }

    if ("working_circuits" in attributes) {
        maxAttributes["max_circuits"] = stats["tech"];
    }

    if ("faith" in stats) {
        const max = Math.floor(stats["faith"] / 2);
        maxAttributes["max_scripture_points"] = max === 0 ? 1 : max;
    }

    if ("blood_points" in attributes) {
        maxAttributes["max_blood_points"] = 20 * stats["lineage"];
    }

    if ("circuits" in attributes) {
        maxAttributes["max_circuits"] = Math.floor(stats["tech"] / 2) + 1;
    }

    return maxAttributes;
}
