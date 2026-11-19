<?php

use App\Filament\Resources\Spells\Pages\CreateSpell;
use App\Helpers\GlossaryMarkup;
use App\Models\Effect;
use App\Models\Spell;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Pest\Livewire\livewire;

test('every glossary term in the seeded game texts names a seeded effect', function () {
    $this->seed();

    $columns = [
        'effects' => ['description'],
        'spells' => ['description'],
        'schools' => ['description'],
        'sonatas' => ['description'],
        'sonata_abilities' => ['description'],
        'scripture_abilities' => ['description'],
        'miracles' => ['description'],
        'advantages' => ['description'],
        'systems' => ['description'],
        'subsystems' => ['description'],
        'mystic_eyes' => ['passive', 'active'],
    ];
    $linkedTextCount = 0;

    foreach ($columns as $table => $tableColumns) {
        foreach ($tableColumns as $column) {
            foreach (DB::table($table)->whereLike($column, '%[[%')->pluck($column) as $text) {
                expect(GlossaryMarkup::isWellFormed($text))->toBeTrue("Malformed markup in {$table}.{$column}: {$text}")
                    ->and(GlossaryMarkup::unknownTerms($text))->toBe([], "Unknown term in {$table}.{$column}: {$text}");
                $linkedTextCount++;
            }
        }
    }

    expect($linkedTextCount)->toBeGreaterThan(0);
});

describe('admin descriptions', function () {
    beforeEach(function () {
        $this->actingAs(User::forceCreate(['login' => 'admin', 'password' => 'password', 'is_admin' => true]));
        Effect::create(['name' => 'Queimado', 'description' => 'Recebe dano a cada turno.']);
    });

    test('accept glossary terms that name an existing effect', function () {
        livewire(CreateSpell::class)
            ->fillForm(['name' => 'Chama', 'description' => 'Deixa o alvo [[queimado]] e os vizinhos [[Queimado|Queimados]].'])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Spell::where('name', 'Chama')->exists())->toBeTrue();
    });

    test('reject glossary terms that are unknown or malformed', function (string $description) {
        livewire(CreateSpell::class)
            ->fillForm(['name' => 'Chama', 'description' => $description])
            ->call('create')
            ->assertHasFormErrors(['description']);

        expect(Spell::where('name', 'Chama')->exists())->toBeFalse();
    })->with([
        'unknown effect' => 'Deixa o alvo [[Eletrocutado]].',
        'unclosed markup' => 'Deixa o alvo [[Queimado.',
    ]);
});
