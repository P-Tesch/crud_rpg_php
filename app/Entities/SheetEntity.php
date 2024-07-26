<?php

namespace App\Entities;

use App\Http\Controllers\SchoolController;
use App\Models\Advantage;
use App\Models\Item;
use App\Models\Miracle;
use App\Models\Sonata;
use App\Models\Stat;
use App\Models\Blood;
use App\Models\Sheet;
use App\Models\Skill;
use JsonSerializable;
use App\Enums\Alignment;
use App\Enums\SpellTypes;
use App\Models\Scripture;
use App\Enums\Organization;
use App\Http\Controllers\SheetController;
use App\Models\RpgAttribute;
use App\Models\School;

class SheetEntity {

    public int $id;
    public string $name;
    public string $portraitPath;
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
    public ?array $miracles;
    public ?array $schools;
    public ?Scripture $scripture;
    public ?array $sonatas;
    public array $skillsRelations;

    public function __construct(array $args) {
        if (count($args) == 0) {
            return;
        }

        $this->id = $args["id"];
        $this->name = $args["name"];
        $this->portraitPath = $args["portraitPath"];
        $this->description = $args["description"];
        $this->background = $args["background"];
        $this->creationPoints = $args["creationPoints"];
        $this->alignment = Alignment::tryFrom($args["alignment"]);
        $this->organization = Organization::tryFrom($args["organization"]);
        $this->stats = $args["stats"];
        $this->attributes = $args["attributes"];
        $this->skills = $args["skills"];
        $this->advantages = $args["advantages"];
        $this->mysticEyes = $args["mysticEyes"];

        $blood = $args["blood"];
        if (isset($args["blood"])) {
            $this->blood = new Blood($blood);
            $this->blood->id = $blood["id"];
        }

        $scripture = $args["scripture"];
        if (isset($scripture)) {
            $this->scripture = new Scripture($scripture);
            $this->scripture->id = $scripture["id"];
        }

        foreach ($args["items"] as $item) {
            $itemModel = new Item($item);
            $itemModel->id = $item["id"];
            $this->items[] = $itemModel;
        }

        foreach ($args["miracles"] as $miracle) {
            $miracleModel = new Miracle($miracle);
            $miracleModel->id = $miracle["id"];
            $this->miracles[] = $miracleModel;
        }

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
            $this->schools[$schoolModel->name] = ["id" => $schoolModel->id, "level" => $schoolModel->level, "spells" => $spells];
        }

        foreach ($args["sonatas"] as $sonata) {
            $sonataModel = new Sonata($sonata);
            $this->sonatas[] = $sonataModel;
        }

    }

    public static function buildFromModel(Sheet $sheet) : SheetEntity {
        $sheetEntity = new SheetEntity([]);
        return $sheetEntity->build($sheet);
    }

    private function build(Sheet $sheet) : SheetEntity {
        $this->id = $sheet->id;
        $this->name = $sheet->name;
        $this->portraitPath = $sheet->portrait;
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
        $this->sonatas = $sheet->sonatas?->toArray();
        $this->mysticEyes = $sheet->mysticEyes?->toArray();

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
            $this->schools[$school->name] = ["id" => $school->id, "level" => $school->level, "spells" => $spells];
        }

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
    }

    public function update(Sheet $model) {
        $model->name = $this->name;
        $model->portrait = $this->portraitPath;
        $model->description = $this->description;
        $model->background = $this->background;
        $model->creation_points = $this->creationPoints;
        $model->alignment = $this->alignment == null ? null : $this->alignment->value;
        $model->organization = $this->organization == null ? null : $this->organization->value;

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

        /*
        foreach ($this->advantages as $advantage) {
        $advantageModel = ($index = array_search($advantage["id"], $model->advantages->map(function ($adv) {return $adv->id;})->toArray()) != null)
            ? $model->advantages->toArray($index)
            : new Advantage();
        }
        */

        //$model->blood = $this->blood;
        //$model->items = $this->items;
        //$model->miracles = $this->miracles;
        //$model->scripture = $this->scripture;
        //$model->sonatas = $this->sonatas;
        $model->save();
    }
}
