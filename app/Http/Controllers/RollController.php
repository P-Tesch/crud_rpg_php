<?php

namespace App\Http\Controllers;

use App\Entities\SheetEntity;
use App\Enums\SpellTypes;
use App\Helpers\RollHelper;
use App\Models\Item;
use Illuminate\Http\Request;

class RollController extends Controller {

    public function rollSkill(Request $request, SheetController $sheetController) {
        $skill = $request->input("skill");
        $modifiers = (int) $request->input("modifier");

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));
        $stat = $sheet->skillsRelations[$skill];

        return ["subject" => $skill, "name" => $sheet->name, "rolls" => RollHelper::roll([$sheet->skills[$skill], $sheet->stats[$stat], $modifiers])];
    }

    public function rollSpell(Request $request, SheetController $sheetController) {
        $cost = (int) $request->input("cost");
        $spellString = $request->input("spell");
        $schoolString = $request->input("school");

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));
        $recoilRoll = $this->rollRecoil($sheet, $cost);

        $spell = $sheet->schools[$schoolString]["spells"][$spellString];

        switch ($spell["type"]) {
            case SpellTypes::PROJECTILE:
                $spellRoll = RollHelper::roll([$sheet->skills["ranged"], $sheet->stats["magic"]]);
                break;
            case SpellTypes::DIRECT:
                $spellRoll = RollHelper::roll([$sheet->stats["magic"]]);
                break;
            default:
                $spellRoll = null;
        }

        if ($spell["strategy"] != null) {
            $specificRoll = RollHelper::roll(eval($spell->strategy));
        }

        $sheet->update($sheetController->showFromId($sheet->id));

        return ["recoilDamage" => $recoilRoll["recoil"], "cost" => $cost, "subject" => $spellString, "name" => $sheet->name,"rolls" => ["recoil" => $recoilRoll["roll"], "success" => $spellRoll, "specific" => $specificRoll ?? null]];
    }

    public function rollItem(Request $request, SheetController $sheetController) : array {
        $itemId = (int) $request->input("item");

        $item = Item::find($itemId);
        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));

        $roll = $item->strategy != null ? RollHelper::roll($item->strategy) : null;

        return ["subject" => $item->name, "name" => $sheet->name, "rolls" => $roll];
    }

    public function rollGeneric(Request $request) : array {
        $modifier = $request->get("modifier");

        return RollHelper::roll([$modifier]);
    }

    private function rollRecoil(SheetEntity &$sheet, int $cost) : array {
        $sheet->attributes["mana"] -= $cost;
        $recoilRoll = RollHelper::roll([$sheet->skills["tenacity"], $sheet->stats["intelligence"]]);

        if ($recoilRoll["hits"] < $cost) {
            $recoil = (int) floor(($cost - $recoilRoll["hits"]) / 2);
            $sheet->attributes["health"] -= $recoil;
        }

        return ["recoil" => $recoil, "roll" => $recoilRoll];
    }
}
