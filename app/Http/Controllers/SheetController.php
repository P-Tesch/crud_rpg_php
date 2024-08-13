<?php

namespace App\Http\Controllers;

use App\Entities\SheetEntity;
use App\Models\Item;
use App\Models\Stat;
use App\Models\Blood;
use App\Models\Sheet;
use App\Models\Spell;
use App\Models\Effect;
use App\Models\School;
use App\Models\Sonata;
use App\Models\Miracle;
use App\Models\Advantage;
use App\Models\MysticEye;
use App\Models\Scripture;
use App\Models\BloodAbility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\SonataAbility;
use App\Models\ScriptureAbility;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SheetResource;
use App\Models\RpgAttribute;
use App\Models\Skill;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class SheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SheetResource::collection(Sheet::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sheet = new Sheet();
        $sheet->name = $request->get("name");
        $sheet->portrait = $request->get("portrait");
        $sheet->description = $request->get("description");
        $sheet->background = $request->get("background");
        $sheet->creation_points = $request->get("creation_points");
        $sheet->alignment = $request->get("alignment");
        $sheet->organization = $request->get("organization");

        $sheet->save();

        $sheet->schools()->attach($request->get("schools"));
        $sheet->mysticEyes()->attach($request->get("mystic_eyes"));
        $sheet->advantages()->attach($request->get("advantages"));

        foreach ($request->get("stats") as $statArray) {
            $stat = new Stat();
            $stat->key = $statArray["key"];
            $stat->value = $statArray["value"];
            $stat->sheet_id = $sheet->id;
            $stat->save();
        }

        foreach ($request->get("skills") as $skillArray) {
            $skill = new Skill();
            $skill->key = $skillArray["key"];
            $skill->value = $skillArray["value"];
            $skill->referenced_stat = $skillArray["referenced_stat"];
            $skill->sheet_id = $sheet->id;
            $skill->save();
        }

        foreach ($request->get("attributes") as $attributesArray) {
            $attributes = new RpgAttribute();
            $attributes->key = $attributesArray["key"];
            $attributes->value = $attributesArray["value"];
            $attributes->sheet_id = $sheet->id;
            $attributes->save();
        }

        $bloodArray = $request->get("blood");
        if (isset($bloodArray)) {
            $blood = new Blood();
            $blood->name = $bloodArray["name"];
            $blood->impulses = $bloodArray["impulses"];
            $blood->sheet_id = $sheet->id;
            $blood->save();
            foreach ($bloodArray["stats"] as $statArray) {
                $stat = new Stat();
                $stat->key = $statArray["key"];
                $stat->value = $statArray["value"];
                $stat->blood_id = $blood->id;
                $stat->save();
            }

            foreach ($bloodArray["blood_abilities"] as $bloodAbilityArray) {
                $bloodAbility = new BloodAbility();
                $bloodAbility->name = $bloodAbilityArray["name"];
                $bloodAbility->description = $bloodAbilityArray["description"];
                $bloodAbility->strategy = $bloodAbilityArray["strategy"];
                $bloodAbility->blood_id = $blood->id;
                $bloodAbility->save();
            }
        }

        $items = $request->get("items");
        if (isset($items)) {
            foreach ($items as $itemArray) {
                $item = new Item;
                $item->name = $itemArray["name"];
                $item->description = $itemArray["description"];
                $item->strategy = $itemArray["strategy"];
                $item->sheet_id = $sheet->id;
                $item->save();
                foreach ($itemArray["effects"] as $effectArray) {
                    $effect = new Effect;
                    $effect->name = $effectArray["name"];
                    $effect->description = $effectArray["description"];
                    $effect->remaining_turns = $effectArray["remaining_turns"];
                    $effect->strategy = $effectArray["strategy"];
                    $effect->item_id = $item->id;
                    $effect->save();
                }
            }
        }

        $effects = $request->get("effects");
        if(isset($effects)) {
            foreach ($effects as $effectArray) {
                $effect = new Effect;
                $effect->name = $effectArray["name"];
                $effect->description = $effectArray["description"];
                $effect->remaining_turns = $effectArray["remaining_turns"];
                $effect->strategy = $effectArray["strategy"];
                $effect->sheet_id = $sheet->id;
                $effect->save();
            }
        }

        $miracles = $request->get("miracles");
        if(isset($miracles)) {
            foreach ($miracles as $miracleArray) {
                $miracle = new Miracle;
                $miracle->name = $miracleArray["name"];
                $miracle->description = $miracleArray["description"];
                $miracle->strategy = $miracleArray["strategy"];
                $miracle->sheet_id = $sheet->id;
                $miracle->save();
            }
        }

        $scriptureArray = $request->get("scripture");
        if (isset($scriptureArray)) {
            $scripture = new Scripture;
            $scripture->name = $scriptureArray["name"];
            $scripture->description = $scriptureArray["description"];
            $scripture->creation_points = $scriptureArray["creation_points"];
            $scripture->remaining_scripture_points = $scriptureArray["remaining_scripture_points"];
            $scripture->damage = $scriptureArray["damage"];
            $scripture->range = $scriptureArray["range"];
            $scripture->sharpness = $scriptureArray["sharpness"];
            $scripture->double = $scriptureArray["double"];
            $scripture->strategy = $scriptureArray["strategy"];
            $scripture->sheet_id = $sheet->id;
            $scripture->save();
            foreach ($scriptureArray["scripture_abilities"] as $saArray) {
                $sa = new ScriptureAbility;
                $sa->name = $saArray["name"];
                $sa->description = $saArray["description"];
                $sa->level = $saArray["level"];
                $sa->strategy = $saArray["strategy"];
                $sa->scripture_id = $scripture->id;
                $sa->save();
            }
        }

        foreach ($request->get("sonatas") as $sonataArray) {
            $sonata = new Sonata;
            $sonata->name = $sonataArray["name"];
            $sonata->description = $sonataArray["description"];
            $sonata->sheet_id = $sheet->id;
            $sonata->save();
            foreach ($sonataArray["sonata_abilities"] as $saArray) {
                $sa = new SonataAbility;
                $sa->name = $saArray["name"];
                $sa->description = $saArray["description"];
                $sa->level = $saArray["level"];
                $sa->strategy = $saArray["strategy"];
                $sa->sonata_id = $sonata->id;
                $sa->save();
            }
        }

        return $sheet->id;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = Auth::user()->sheet_id;
        return new SheetResource(Sheet::find($id));
    }

    public function showAsModel(Request $request) : Sheet {
        $id = Auth::user()->sheet_id;
        return Sheet::find($id);
    }

    public function showFromId(int $id) {
        return Sheet::find($id);
    }

    public function showAsEntity(Request $request) {
        return SheetEntity::buildFromModel($this->showAsModel($request));
    }

    public function showEntityAsJson(Request $request) {
        return json_encode(SheetEntity::buildFromModel($this->showAsModel($request)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $sheetEntity = new SheetEntity(json_decode($request->getContent(), true));
        $sheetEntity->update($this->showAsModel($request));
        return new Response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return Sheet::destroy($id);
    }
}
