<?php

namespace App\Http\Controllers;

use App\Entities\SheetEntity;
use App\Models\Item;
use App\Models\Stat;
use App\Models\Blood;
use App\Models\Sheet;
use App\Models\Effect;
use App\Models\School;
use App\Models\Miracle;
use App\Models\Scripture;
use App\Models\BloodAbility;
use Illuminate\Http\Request;
use App\Models\ScriptureAbility;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SheetResource;
use App\Models\RpgAttribute;
use App\Models\Skill;
use Illuminate\Http\Response;

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
    public function store(Request $request) : int
    {
        $sheet = new Sheet();
        $sheet->name = $request->input("sheet")["name"];
        $sheet->portrait = $request->input("sheet")["portrait"];
        $sheet->description = $request->input("sheet")["description"];
        $sheet->background = $request->input("sheet")["background"];
        $sheet->creation_points = $request->input("sheet")["creation_points"];
        $sheet->alignment = $request->input("sheet")["alignment"];
        $sheet->organization = $request->input("sheet")["organization"];

        $sheet->save();

        if ($sheet->organization) {
            $item = $this->getOrganizationItem($sheet->organization);
            $item->sheet_id = $sheet->id;
            $item->save();
        }

        if ($sheet->alignment) {
            $sheet->schools()->attach(School::where("name", "=", "Fundamentos gerais - " . $this->getAlignmentAlias($sheet->alignment))->where("level", "=", 1)->get());
        }
        $sheet->schools()->attach($request->input("sheet")["schools"]);
        $sheet->mysticEyes()->attach($request->input("sheet")["mystic_eyes"]);
        $sheet->advantages()->attach($request->input("sheet")["advantages"]);
        $sheet->sonatas()->attach($request->input("sheet")["sonatas"]);

        foreach ($request->input("sheet")["stats"] as $statArray) {
            $stat = new Stat();
            $stat->key = $statArray["key"];
            $stat->value = $statArray["value"];
            $stat->sheet_id = $sheet->id;
            $stat->save();
        }

        foreach ($request->input("sheet")["skills"] as $skillArray) {
            $skill = new Skill();
            $skill->key = $skillArray["key"];
            $skill->value = $skillArray["value"];
            $skill->referenced_stat = $skillArray["referenced_stat"];
            $skill->sheet_id = $sheet->id;
            $skill->save();
        }

        foreach ($request->input("sheet")["attributes"] as $attributesArray) {
            $attributes = new RpgAttribute();
            $attributes->key = $attributesArray["key"];
            $attributes->value = $attributesArray["value"];
            $attributes->sheet_id = $sheet->id;
            $attributes->save();
        }

        $bloodArray = $request->input("sheet")["blood"];
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

        $items = $request->input("sheet")["items"];
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

        $effects = $request->input("sheet")["effects"];
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

        $miracles = $request->input("sheet")["miracles"];
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

        $scriptureArray = $request->input("sheet")["scripture"];
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

    public function showFromId(int $id) : Sheet {
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

    private function getAlignmentAlias(string $alignment) : string {
        return match ($alignment) {
            "fire" => "Fogo",
            "water" => "Água",
            "air" => "Ar",
            "earth" => "Terra",
            "arcana" => "Arcana",
            "void" => "Vazio",
            "ice" => "Gelo",
            "electricity" => "Eletricidade"
        };
    }

    private function getOrganizationItem(string $organization) : Item {
        return match ($organization) {
            "executors" => new Item([
                "name" => "Chaves negras",
                "description" => "Chaves negras são milagres que podem ser materializados na forma de espadas curtas, podendo ser utilizadas corpo a corpo ou arremessadas. O usuário pode materializar um número de chaves igual ao seu atributo de Fé, materializá-las ou desmaterializá-las são um ação gratuita. Quando uma chave negra é fincada em um inimigo, o usuário pode optar por mantê-la presa, onde apenas o usuário pode removê-la. Para cada chave fincada no inimigo, ele tem sua Resistência diminuída em 1. Quando fincadas, as chaves negras duram por Fé turnos. As chaves tem 1 de dano bônus em ataques.",
                "damage" => 1,
                "strategy" => null
            ]),
            "chivalry" => new Item([
                "name" => "Santa alabarda",
                "description" => "Ela é uma arma corpo a corpo, com um quadrado extra de alcance e Fé de dano, capaz de causar dano a espíritos e fantasmas, além de duplicar todo o dano contra raças místicas e hereges, porém causa metade de dano contra humanos.",
                "damage" => 1,
                "strategy" => null
            ]),
            "brotherhood" => new Item([
                "name" => "Chama sagrada",
                "description" => "Uma arma que se permite ser materializada em qualquer forma que o usuário desejar, tendo Fé de dano e, no caso de ser uma arma a distância, tem alcance igual a 2*Fé.",
                "damage" => 1,
                "strategy" => null
            ]),
            "exorcists" => new Item([
                "name" => "Papéis sentinela",
                "description" => "Esses papeis podem ser posicionados(no caso de tentar posicioná-los à distância, como arremessando preso a uma adaga é necessário rolar um dado de Combate a distância). Depois de posicionados, eles se ligam formando um campo(um não faz nada, dois forma uma linha, três ou mais formam um plano). Quando o campo é ativado, o usuário declara um tipo de criatura(Morto-vivo, mago, fantasma, etc.) todos do tipo declarado dentro da área perdem em todas as suas rolagens dados iguais a ⅓ Fé. Além disso, o usuário pode pode destruir o campo, assim todos do tipo declarado dentro do campo devem rolar um dado de Tenacidade contra a quantidade de papeis destruídos e os que falharem são Atordoados pela diferença de acertos em turnos.",
                "damage" => null,
                "strategy" => null
            ])
        };
    }
}
