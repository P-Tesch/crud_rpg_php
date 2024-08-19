<?php

namespace App\Entities;

use App\Http\Controllers\AdvantagesController;
use App\Http\Controllers\MiraclesController;
use App\Http\Controllers\MysticEyesController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ScriptureAbilitiesController;
use App\Http\Controllers\SonatasController;
use App\Models\Item;
use App\Models\Sonata;
use App\Models\Blood;
use App\Models\Sheet;
use App\Enums\Alignment;
use App\Enums\SpellTypes;
use App\Models\Scripture;
use App\Enums\Organization;
use Illuminate\Support\Facades\Storage;

class SheetEntity {

    public int $id;
    public string $name;
    public ?string $portrait;
    public string $description;
    public string $background;
    public int $creationPoints;
    public ?Alignment $alignment;
    public ?Organization $organization;
    public array $stats;
    public array $attributes;
    public array $maxAttributes;
    public array $skills;
    public array $advantages;
    public array $mysticEyes;
    public ?Blood $blood;
    public array $items;
    public array $miracles;
    public ?array $schools;
    public ?Scripture $scripture;
    public ?array $sonatas;
    public array $skillsRelations;
    public array $classes;

    public function __construct(array $args) {
        if (count($args) == 0) {
            return;
        }

        $this->id = $args["id"];
        $this->name = $args["name"];
        $this->portrait = $args["portrait"];
        $this->description = $args["description"];
        $this->background = $args["background"];
        $this->creationPoints = $args["creationPoints"];
        $this->alignment = Alignment::tryFrom($args["alignment"]);
        $this->organization = Organization::tryFrom($args["organization"]);
        $this->stats = $args["stats"];
        $this->attributes = $args["attributes"];
        $this->skills = $args["skills"];

        $blood = $args["blood"];
        if (isset($args["blood"])) {
            $this->blood = new Blood($blood);
            $this->blood->id = $blood["id"];
        }

        $scripture = $args["scripture"];
        if (isset($scripture)) {
            $this->scripture = new Scripture($args["scripture"]);
            $scriptureAbilities = [];
            foreach ($args["scripture"]["scriptureAbilities"] as $scriptureAbility) {
                $scriptureAbilities[] = ScriptureAbilitiesController::findByNameAndLevel($scriptureAbility["name"], $scriptureAbility["level"]);
            }
            $this->scripture->scriptureAbilities = $scriptureAbilities;
        }

        foreach ($args["items"] as $item) {
            $itemModel = new Item($item);
            $itemModel->id = $item["id"];
            $this->items[] = $itemModel;
        }

        $this->miracles = [];
        foreach ($args["miracles"] as $miracle) {
            $this->miracles[] = MiraclesController::findByName($miracle["name"]);
        }

        $this->schools = [];
        foreach ($args["schools"] as $name => $school) {
            $schoolModel = SchoolController::findByNameAndLevel($name, $school["level"]);
            $spells = [];
            foreach ($schoolModel->spells as $spell) {
                $spells[$spell->name] = [
                    "type" => SpellTypes::tryFrom($spell->type),
                    "description" => $spell->description,
                    "strategy" => $spell->strategy
                ];
            }
            $this->schools[$schoolModel->name] = ["id" => $schoolModel->id, "level" => $schoolModel->level, "cost" => $schoolModel->cost, "spells" => $spells];
        }

        $mysticEyes = [];
        foreach ($args["mysticEyes"] as $mysticEye) {
            $mysticEyes[] = MysticEyesController::findByName($mysticEye["name"]);
        }
        $this->mysticEyes = $mysticEyes;

        $advantages = [];
        foreach ($args["advantages"] as $advantage) {
            $advantages[] = AdvantagesController::findByNameAndLevel($advantage["name"], $advantage["level"]);
        }
        $this->advantages = $advantages;

        $this->sonatas = [];
        foreach ($args["sonatas"] as $name => $sonata) {
            $sonataModel = SonatasController::findByName($name);
            $this->sonatas[$sonataModel->name] = [
                "id" => $sonataModel->id,
                "abilities" => []
            ];
        }

        $this->setClasses();

    }

    public static function buildFromModel(Sheet $sheet) : SheetEntity {
        $sheetEntity = new SheetEntity([]);
        return $sheetEntity->build($sheet);
    }

    private function build(Sheet $sheet) : SheetEntity {
        $this->id = $sheet->id;
        $this->name = $sheet->name;
        $this->portrait = $sheet->portrait;
        $this->description = $sheet->description;
        $this->background = $sheet->background;
        $this->creationPoints = $sheet->creation_points;
        $this->alignment = Alignment::tryFrom($sheet->alignment);
        $this->organization = Organization::tryFrom($sheet->organization);
        $this->advantages = $sheet->advantages?->toArray();
        $this->blood = $sheet->blood;
        $this->items = $sheet->items?->toArray();
        $this->miracles = $sheet->miracles?->toArray();
        $this->scripture = $sheet->scripture;
        if ($this->scripture != null) {
            $this->scripture->scriptureAbilities = $sheet->scripture->scriptureAbilities?->toArray();
        }
        $this->mysticEyes = $sheet->mysticEyes?->all();

        foreach ($sheet->stats as $stat) {
            $this->stats[$stat->key] = $stat->value;
        }

        foreach ($sheet->rpgAttributes as $attribute) {
            $this->attributes[$attribute->key] = $attribute->value;
        }

        foreach ($sheet->skills as $skill) {
            $this->skills[$skill->key] = $skill->value;
            $this->skillsRelations[$skill->key] = $skill->referenced_stat;
        }

        $this->calculateMaxAttributes();

        $this->schools = [];
        foreach ($sheet->schools as $school) {
            $spells = [];
            foreach ($school->spells as $spell) {
                $spells[$spell->name] = [
                    "type" => SpellTypes::tryFrom($spell->type),
                    "description" => $spell->description,
                    "strategy" => $spell->strategy
                ];
            }
            $this->schools[$school->name] = ["id" => $school->id, "level" => $school->level, "spells" => $spells, "cost" => $school->cost];
        }

        $this->miracles = [];
        foreach ($sheet->miracles as $miracle) {
            $this->miracles[] = MiraclesController::findByName($miracle["name"]);
        }

        $this->sonatas = [];
        foreach ($sheet->sonatas as $sonata) {
            $this->sonatas[$sonata->name] = [
                "id" => $sonata->id,
                "abilites" => []
            ];
            foreach ($sonata->sonataAbilities as $ability) {
                if ($sheet->sonataAbilities->contains($ability)) {
                    $this->sonatas[$sonata->name]["abilities"] = $ability;
                }
            }
        }

        $this->setClasses();

        return $this;
    }

    private function calculateMaxAttributes() {
        $this->maxAttributes = [];
        $this->maxAttributes["max_health"] = 2 + 2 * $this->stats["endurance"] + $this->stats["strength"];
        $this->maxAttributes["max_initiative"] = $this->stats["agility"] + $this->stats["perception"];
        $this->maxAttributes["max_movement"] = 2 + $this->skills["speed"];
        if (array_key_exists("mana", $this->attributes)) {
            $this->maxAttributes["max_mana"] = 20 * $this->stats["magic"];
        }
        if (array_key_exists("energy", $this->attributes)) {
            $this->maxAttributes["max_energy"] = 20 * $this->stats["blood"];
        }
        if (array_key_exists("working_circuits", $this->attributes)) {
            $this->maxAttributes["max_circuits"] = $this->stats["tech"];
        }
        if (array_key_exists("faith", $this->stats)) {
            $max = (int) floor($this->stats["faith"] / 2);
            $this->maxAttributes["max_scripture_points"] = $max == 0 ? 1 : $max;
        }
        if (array_key_exists("blood_points", $this->attributes)) {
            $this->maxAttributes["max_blood_points"] = 20 * $this->stats["lineage"];
        }
    }

    public function update(Sheet $model) {
        $model->name = $this->name;
        $model->description = $this->description;
        $model->background = $this->background;
        $model->creation_points = $this->creationPoints;
        $model->alignment = $this->alignment == null ? null : $this->alignment->value;
        $model->organization = $this->organization == null ? null : $this->organization->value;

        if ($this->portrait != $model->portrait) {
            $separated = explode(";base64,", $this->portrait);
            $base64 = $separated[1];
            $extension = explode("image/", $separated[0])[1];
            $portraitPath = "/portraits/" . time() . "." . $extension;
            Storage::put("/public" . $portraitPath, base64_decode($base64));
            $model->portrait = "/storage" . $portraitPath;
        }

        foreach ($this->stats as $key => $value) {
            foreach ($model->stats as &$stat) {
                $stat->value = $stat["key"] == $key ? $value : $stat["value"];
                $stat->save();
            }
        }

        foreach ($this->skills as $key => $value) {
            foreach ($model->skills as &$skill) {
                $skill->value = $skill["key"] == $key ? $value : $skill["value"];
                $skill->save();
            }
        }

        foreach ($this->attributes as $key => $value) {
            foreach ($model->rpgAttributes as &$attribute) {
                $attribute->value = $attribute["key"] == $key ? $value : $attribute["value"];
                $attribute->save();
            }
        }

        $schools = [];
        foreach ($this->schools as $school) {
            $schools[] = $school["id"];
        }

        $model->schools()->sync($schools, true);

        $mysticEyes = [];
        foreach ($this->mysticEyes as $mysticEye) {
            $mysticEyes[$mysticEye->id] = ["current_cooldown" => $mysticEye->pivot?->current_cooldown ?: $mysticEye->cooldown];
        }

        $model->mysticEyes()->sync($mysticEyes, true);

        $advantages = [];
        foreach ($this->advantages as $advantage) {
            $advantages[] = $advantage->id;
        }

        $model->advantages()->sync($advantages, true);

        $miracles = [];
        foreach($this->miracles as $miracle) {
            $miracles[] = $miracle["id"];
        }

        $model->miracles()->sync($miracles, true);

        if ($model->scripture != null) {
            $model->scripture->name = $this->scripture->name;
            $model->scripture->description = $this->scripture->description;
            $model->scripture->damage = $this->scripture->damage;
            $model->scripture->range = $this->scripture->range;
            $model->scripture->sharpness = $this->scripture->sharpness;
            $model->scripture->double = $this->scripture->double;
            $model->scripture->remaining_scripture_points = $this->scripture->remaining_scripture_points;
            $model->scripture->creation_points = $this->scripture->creation_points;

            $model->scripture->save();

            $scriptureAbilities = [];
            foreach ($this->scripture->scriptureAbilities as $scriptureAbility) {
                $scriptureAbilities[] = $scriptureAbility->id;
            }

            $model->scripture->scriptureAbilities()->sync($scriptureAbilities, true);
        }

        $sonatas = [];
        foreach (array_keys($this->sonatas) as $sonataName) {
            $sonatas[] = $this->sonatas[$sonataName]["id"];
        }
        $model->sonatas()->sync($sonatas, true);

        $sonataAbilities = [];
        foreach ($this->sonatas as $sonata) {
            foreach ($sonata["abilities"] as $ability) {
                $sonataAbilities[] = $ability->id;
            }
        }
        $model->SonataAbilities()->sync($sonataAbilities, true);

        $this->setClasses();
        $model->save();
    }

    private function setClasses() {
        $this->classes = [];
        $this->classes["isMage"] = array_key_exists("magic", $this->stats);
        $this->classes["isCleric"] = array_key_exists("faith", $this->stats);
        $this->classes["isVampire"] = array_key_exists("lineage", $this->stats);
        $this->classes["isMagiteck"] = array_key_exists("tech", $this->stats);
        $this->classes["isHybrid"] = array_key_exists("blood", $this->stats);
    }
}
