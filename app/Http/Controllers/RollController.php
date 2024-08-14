<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Enums\SpellTypes;
use App\Helpers\RollHelper;
use Illuminate\Http\Request;
use App\Entities\SheetEntity;
use App\Entities\RollHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;

class RollController extends Controller {

    private $rollHistory;

    public function __construct() {
        $this->rollHistory = Cache::get("rollsTable", new RollHistory());
    }

    public function index(Request $request) {
        return $this->rollHistory->getRolls();
    }

    public function rollSkill(Request $request, SheetController $sheetController) : void {
        $skill = $request->input("skill");
        $modifiers = (int) $request->input("modifier");

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));
        $stat = $sheet->skillsRelations[$skill];

        $this->broadcastAndStore(["portrait" => $sheet->portrait, "type" => "skill", "id" => $sheet->id, "subject" => $skill, "name" => $sheet->name, "rolls" => RollHelper::roll([$sheet->skills[$skill], $sheet->stats[$stat], $modifiers])]);
    }

    public function rollSpell(Request $request, SheetController $sheetController) : void {
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

        $this->broadcastAndStore(["portrait" => $sheet->portrait, "type" => "spell", "id" => $sheet->id, "recoilDamage" => $recoilRoll["recoil"], "cost" => $cost, "subject" => $spellString, "name" => $sheet->name,"rolls" => ["recoil" => $recoilRoll["roll"], "success" => $spellRoll, "specific" => $specificRoll ?? null]]);
    }

    public function rollItem(Request $request, SheetController $sheetController) : void {
        $itemId = (int) $request->input("item");

        $item = Item::find($itemId);
        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));

        $roll = $item->strategy != null ? RollHelper::roll($item->strategy) : null;

        $this->broadcastAndStore(["portrait" => $sheet->portrait, "type" => "item", "subject" => $item->name, "name" => $sheet->name, "id" => $sheet->id, "rolls" => $roll]);
    }

    public function rollMysticEye(Request $request, SheetController $sheetController, MysticEyesController $mysticEyesController) : void {
        $eyeId = (int) $request->input("eye");
        $targetId = (int) $request->input("target");

        $eye = $mysticEyesController->show($eyeId);

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));
        $target = SheetEntity::buildFromModel($sheetController->showFromId($targetId));

        // TODO: #13 Roll from strategy and apply effects to $target
        $roll = RollHelper::roll([5]); // Placeholder

        $this->broadcastAndStore(["portrait" => $sheet->portrait, "id" => $sheet->id, "type" => "mystic_eye", "subject" => $eye->name, "name" => $sheet->name, "target" => $target->name, "rolls" => $roll]);
    }

    public function rollGeneric(Request $request, SheetController $sheetController) : void {
        $modifier = $request->get("modifier");

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));

        $this->broadcastAndStore(["portrait" => $sheet->portrait, "type" => "generic", "name" => $sheet->name, "rolls" => RollHelper::roll([$modifier])]);
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

    private function broadcastAndStore(array $rolls) {
        $this->rollHistory->addRoll($rolls);

        Broadcast::on("rolls")
        ->as("rollsTableUpdated")
        ->with($this->rollHistory->getRolls())
        ->sendNow();

        Cache::put("rollsTable", $this->rollHistory);
    }
}
