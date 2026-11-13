<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sheet;
use App\Models\Spell;
use App\Models\Strategy;
use App\Enums\SpellTypes;
use App\Helpers\RollHelper;
use App\Services\Strategy\StrategyEngine;
use App\Helpers\StrategyException;
use Illuminate\Http\Request;
use App\Entities\RollHistory;
use App\Entities\SheetEntity;
use App\Entities\StrategyInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Broadcast;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class RollController extends Controller
{
    /** @var RollHistory */
    private $rollHistory;

    public function __construct()
    {
        $this->rollHistory = new RollHistory(Cache::get("rollsTable", []));
    }

    /**
     * List all rolls
     * @param Request $request
     * @return array<int, array<string, mixed>>
     */
    public function index(Request $request): array
    {
        return $this->rollHistory->getRolls();
    }

    /**
     * Roll a skill
     * @param Request $request
     * @param SheetController $sheetController
     * @return void
     */
    public function rollSkill(Request $request, SheetController $sheetController): void
    {
        $skill = $request->input("skill");
        $modifiers = (int) $request->input("modifier");

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));
        $stat = $sheet->skillsRelations[$skill];

        $this->broadcastAndStore([
            "portrait" => $sheet->portrait,
            "type" => "skill",
            "id" => $sheet->id,
            "subject" => $skill,
            "name" => $sheet->name,
            "rolls" => [
                "skill" => RollHelper::roll([$sheet->skills[$skill], $sheet->stats[$stat], $modifiers]),
            ],
        ],
        );
    }

    /**
     * Roll a spell
     * @param Request $request
     * @param SheetController $sheetController
     * @param StrategyEngine $strategyEngine
     * @return void
     */
    public function rollSpell(Request $request, SheetController $sheetController, StrategyEngine $strategyEngine): void
    {
        $cost = (int) $request->input("cost");
        $spellString = $request->input("spell");
        $schoolString = $request->input("school");

        $sheetModel = $sheetController->showAsModel($request);
        $sheet = SheetEntity::buildFromModel($sheetModel);
        $recoilRoll = $this->rollRecoil($sheet, $cost);

        $spell = $sheet->schools[$schoolString]["spells"][$spellString];
        $spellModel = Spell::where("name", $spellString)
            ->whereHas("schools", fn ($query) => $query->whereKey($sheet->schools[$schoolString]["id"]))
            ->first();

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

        $targets = $this->resolveTargets($request, $sheet, $sheetController);
        $strategy = $this->runStrategy($strategyEngine, $spellModel?->strategy, $sheet, array_column($targets, "entity"), new StrategyInput(cost: $cost));

        $sheet->update($sheetModel);
        $this->updateTargets($targets);

        $this->broadcastAndStore([
            "portrait" => $sheet->portrait,
            "type" => "spell",
            "id" => $sheet->id,
            "recoilDamage" => $recoilRoll["recoil"],
            "cost" => $cost,
            "subject" => $spellString,
            "name" => $sheet->name,
            "rolls" => [
                "recoil" => $recoilRoll["roll"],
                "success" => $spellRoll,
                "specific" => $strategy["roll"] ?? null,
            ],
            "strategy" => $strategy["details"] ?? null,
        ],
        );
    }

    /**
     * Roll an item
     * @param Request $request
     * @param SheetController $sheetController
     * @param StrategyEngine $strategyEngine
     * @return void
     */
    public function rollItem(Request $request, SheetController $sheetController, StrategyEngine $strategyEngine): void
    {
        $itemId = (int) $request->input("item");

        $sheetModel = $sheetController->showAsModel($request);

        $item = Item::where("sheet_id", $sheetModel->id)->find($itemId);
        if (is_null($item)) {
            throw new NotFoundResourceException("Item não encontrado");
        }

        $sheet = SheetEntity::buildFromModel($sheetModel);

        $targets = $this->resolveTargets($request, $sheet, $sheetController);
        $strategy = $this->runStrategy($strategyEngine, $item->strategy, $sheet, array_column($targets, "entity"));

        $sheet->update($sheetModel);
        $this->updateTargets($targets);

        $this->broadcastAndStore([
            "portrait" => $sheet->portrait,
            "type" => "item",
            "subject" => $item->name,
            "name" => $sheet->name,
            "id" => $sheet->id,
            "rolls" => $strategy["roll"] ?? null,
            "strategy" => $strategy["details"] ?? null,
        ],
        );
    }

    /**
     * Roll a mystic eye
     * @param Request $request
     * @param SheetController $sheetController
     * @param MysticEyesController $mysticEyesController
     * @param StrategyEngine $strategyEngine
     * @return void
     */
    public function rollMysticEye(Request $request, SheetController $sheetController, MysticEyesController $mysticEyesController, StrategyEngine $strategyEngine): void
    {
        $eyeId = (int) $request->input("eye");

        $eye = $mysticEyesController->show($eyeId);
        if (!isset($eye)) {
            return;
        }

        $sheetModel = $sheetController->showAsModel($request);
        if (!$sheetModel->mysticEyes->contains($eye)) {
            throw new NotFoundResourceException("Olho místico não encontrado");
        }

        $sheet = SheetEntity::buildFromModel($sheetModel);
        $targets = $this->resolveTargets($request, $sheet, $sheetController);
        $target = $targets[0]["entity"] ?? null;

        $strategy = $this->runStrategy($strategyEngine, $eye->activeStrategy, $sheet, array_column($targets, "entity"));

        $sheet->update($sheetModel);
        $this->updateTargets($targets);

        $this->broadcastAndStore([
            "portrait" => $sheet->portrait,
            "id" => $sheet->id,
            "type" => "mystic_eye",
            "subject" => $eye->name,
            "name" => $sheet->name,
            "target" => $target?->name,
            "rolls" => [
                "mystic_eye" => $strategy["roll"] ?? null,
            ],
            "strategy" => $strategy["details"] ?? null,
        ],
        );
    }

    /**
     * Roll from a pre determined number
     * @param Request $request
     * @param SheetController $sheetController
     * @return void
     */
    public function rollGeneric(Request $request, SheetController $sheetController): void
    {
        $modifier = $request->input("modifier");

        $sheet = SheetEntity::buildFromModel($sheetController->showAsModel($request));

        $this->broadcastAndStore([
            "portrait" => $sheet->portrait,
            "type" => "generic",
            "name" => $sheet->name,
            "amount" => $modifier,
            "rolls" => [
                "success" => RollHelper::roll([$modifier]),
            ],
        ],
        );
    }

    /**
     * Build the optional target sheet from the "target" input. Targeting your own sheet reuses the
     * user's entity, so both are saved as one.
     * @param Request $request
     * @param SheetEntity $user
     * @param SheetController $sheetController
     * @return list<array{entity: SheetEntity, model: ?Sheet}>
     */
    private function resolveTargets(Request $request, SheetEntity $user, SheetController $sheetController): array
    {
        $targetId = $request->integer("target");
        if ($targetId === 0) {
            return [];
        }

        if ($targetId === $user->id) {
            return [["entity" => $user, "model" => null]];
        }

        $model = $sheetController->showFromId($targetId);

        return [["entity" => SheetEntity::buildFromModel($model), "model" => $model]];
    }

    /**
     * Persist target sheets changed by a strategy (the user's own sheet is saved by the caller)
     * @param list<array{entity: SheetEntity, model: ?Sheet}> $targets
     * @return void
     */
    private function updateTargets(array $targets): void
    {
        foreach ($targets as $target) {
            if ($target["model"] !== null) {
                $target["entity"]->update($target["model"]);
            }
        }
    }

    /**
     * Run an action's Lua strategy. A failing strategy changes no sheet and reports its error in the details.
     * @param StrategyEngine $strategyEngine
     * @param ?Strategy $strategy
     * @param SheetEntity $user
     * @param list<SheetEntity> $targets
     * @param StrategyInput $input
     * @return ?array{roll: ?array{rolls: list<int>, hits: int}, details: array<string, mixed>}
     */
    private function runStrategy(StrategyEngine $strategyEngine, ?Strategy $strategy, SheetEntity $user, array $targets, StrategyInput $input = new StrategyInput()): ?array
    {
        if ($strategy === null) {
            return null;
        }

        try {
            $result = $strategyEngine->run($strategy->code, $user, $targets, $input);
        } catch (StrategyException $exception) {
            report($exception);

            return ["roll" => null, "details" => ["error" => $exception->getMessage()]];
        }

        return ["roll" => $result->getMainRoll(), "details" => $result->toArray()];
    }

    /**
     * Roll a spell's recoil
     * @param SheetEntity $sheet
     * @param int $cost
     * @return array<string, mixed>
     */
    private function rollRecoil(SheetEntity &$sheet, int $cost): array
    {
        $sheet->attributes["mana"] -= $cost;
        $recoilRoll = RollHelper::roll([$sheet->skills["tenacity"], $sheet->stats["intelligence"]]);
        $recoil = 0;

        if ($recoilRoll["hits"] < $cost) {
            $recoil = (int) floor(($cost - $recoilRoll["hits"]) / 2);
            $sheet->attributes["health"] -= $recoil;
        }

        return [
            "recoil" => $recoil,
            "roll" => $recoilRoll,
        ];
    }

    /**
     * Broadcast the result to the websocket and store the history in cache
     * @param array<string, mixed> $rolls
     * @return void
     */
    private function broadcastAndStore(array $rolls)
    {
        $this->rollHistory->addRoll($rolls);

        Broadcast::on("rolls")
        ->as("rollsTableUpdated")
        ->with($this->rollHistory->getRolls())
        ->sendNow();

        Cache::put("rollsTable", $this->rollHistory->getRolls());
    }
}
