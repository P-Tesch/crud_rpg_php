<?php

namespace Database\Seeders\Strategy;

use App\Models\Spell;
use App\Models\Strategy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

/**
 * Writes the Lua strategy of every spell described in database/seeders/data/Magic.txt.
 *
 * Must run after the schools and spells are seeded. Re-running it updates the existing
 * strategies in place instead of creating new rows.
 */
class MagicStrategySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spellsBySchool = $this->spellsBySchool(Spell::with(["schools", "strategy"])->get());

        $strategies = [
            ...$this->elementalFundamentalsStrategies(),
            ...$this->gemNumberStarCraftStrategies(),
            ...$this->curseBodySummonFieldStrategies(),
        ];

        foreach ($strategies as $schoolName => $spellStrategies) {
            foreach ($spellStrategies as $spellName => $code) {
                $spell = $spellsBySchool[$this->normalizeName($schoolName)][$this->normalizeName($spellName)] ?? null;
                if ($spell === null) {
                    $this->command?->warn("Spell not found: {$schoolName} / {$spellName}");

                    continue;
                }

                $this->saveStrategy($spell, $code);
            }
        }
    }

    /**
     * Create or update the spell's strategy with the given Lua code.
     */
    private function saveStrategy(Spell $spell, string $code): void
    {
        $strategy = $spell->strategy ?? new Strategy();
        $strategy->code = $code;
        $strategy->save();

        $spell->strategy()->associate($strategy)->save();
    }

    /**
     * Index spells by normalized school name and spell name. Spell names repeat across schools (e.g. "Avanço").
     *
     * @param Collection<int, Spell> $spells
     * @return array<string, array<string, Spell>>
     */
    private function spellsBySchool(Collection $spells): array
    {
        $index = [];
        foreach ($spells as $spell) {
            foreach ($spell->schools as $school) {
                $index[$this->normalizeName($school->name)][$this->normalizeName($spell->name)] = $spell;
            }
        }

        return $index;
    }

    /**
     * Magic.txt has stray spaces in some names ("Cofre arcano :", "Reforço  elemental de água").
     */
    private function normalizeName(string $name): string
    {
        return mb_strtolower(trim((string) preg_replace("/\s+/u", " ", $name)));
    }

    /**
     * Fundamentos gerais: Fogo, Água, Ar, Terra, Arcana, Vazio, Eletricidade and Gelo.
     *
     * @return array<string, array<string, string>>
     */
    private function elementalFundamentalsStrategies(): array
    {
        return [
            "Fundamentos gerais - Fogo" => [
                "Toque flamejante" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque flamejante"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque flamejante.")
                      return
                    end
                    rpg.damage(1, mp)
                    rpg.applyEffect(1, "Cauterizado", mp, mp)
                    local message = target.name .. " recebe " .. mp .. " de dano e é Cauterizado em " .. mp .. " por " .. mp .. " turnos."
                    if rpg.roll(1, "Queimadura").hits >= 1 then
                      rpg.applyEffect(1, "Queimado", 1, mp)
                      message = message .. " Também é Queimado em 1 por " .. mp .. " turnos."
                    end
                    rpg.log(message)
                    LUA,
                "Dardo de chamas" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range is MP+1 squares; needs positions to validate
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Dardo de chamas"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Dardo de chamas.")
                      return
                    end
                    local damage = math.max(mp - 2, 0)
                    rpg.damage(1, damage)
                    rpg.applyEffect(1, "Cauterizado", mp, mp)
                    local message = target.name .. " recebe " .. damage .. " de dano e é Cauterizado em " .. mp .. "."
                    if rpg.roll(1, "Queimadura").hits >= 1 then
                      rpg.applyEffect(1, "Queimado", 1, mp)
                      message = message .. " Também é Queimado em 1 por " .. mp .. " turnos."
                    end
                    rpg.log(message)
                    LUA,
                "Esfera de fogo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    -- TODO: summon a controllable fire sphere with strength MP that drops by 1 per turn and moves up to its remaining strength in squares; needs summons/positions
                    rpg.log("Esfera de fogo invocada com força " .. mp .. ".")
                    if not target then
                      rpg.log("Nenhum alvo selecionado para o ataque da esfera.")
                      return
                    end
                    -- TODO: the sphere should attack on later turns too; only the first attack is resolved now
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Esfera de fogo"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou da Esfera de fogo.")
                      return
                    end
                    local damage = math.max(mp - 1, 0)
                    rpg.damage(1, damage)
                    rpg.applyEffect(1, "Cauterizado", mp, mp)
                    local message = target.name .. " recebe " .. damage .. " de dano e é Cauterizado em " .. mp .. "."
                    if rpg.roll(1, "Queimadura").hits >= 1 then
                      rpg.applyEffect(1, "Queimado", 1, mp)
                      message = message .. " Também é Queimado em 1 por " .. mp .. " turnos."
                    end
                    rpg.log(message)
                    LUA,
                "Energia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: the extra action per turn is not enforced by the system; tracked with the Energizado effect
                    rpg.applyEffect("user", "Energizado", 2, mp)
                    -- TODO: when Energizado ends, apply Exausto (power 2, -2 dice on all rolls) until the user rests; needs an on-expire trigger
                    rpg.log(ctx.user.name .. " pode realizar duas ações por turno durante " .. mp .. " turnos. Ao fim, ficará Exausto até descansar.")
                    LUA,
                "Explosão" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    -- TODO: range of 10 squares and explosion radius of 5; needs positions to select everyone caught
                    if #ctx.targets == 0 then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    local attackPool = (user.skills.ranged or 0) + mp
                    for i, t in ipairs(ctx.targets) do
                      local dodge = rpg.contest(
                        (t.skills.acrobatics or 0) + (t.stats.agility or 0),
                        attackPool,
                        "Esquiva de " .. t.name
                      )
                      if dodge.success then
                        rpg.log(t.name .. " desviou completamente da explosão.")
                      else
                        rpg.damage(i, mp)
                        rpg.applyEffect(i, "Cauterizado", mp, mp)
                        local message = t.name .. " recebe " .. mp .. " de dano e é Cauterizado em " .. mp .. "."
                        if rpg.roll(1, "Queimadura de " .. t.name).hits >= 1 then
                          rpg.applyEffect(i, "Queimado", 1, mp)
                          message = message .. " Também é Queimado em 1 por " .. mp .. " turnos."
                        end
                        rpg.log(message)
                      end
                    end
                    LUA,
                "Ragnarok" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local range = (user.stats.magic or 0) + mp
                    rpg.applyEffect("user", "Concentrado")
                    -- TODO: the area (range Magia+MP) takes one turn to form; afterwards, every turn everyone inside is Queimado 1, Cauterizado 1 and takes MP damage; needs area/positions and per-turn triggers
                    -- TODO: upkeep of ½ initial MP per turn while Concentrado; needs per-turn upkeep
                    rpg.log(user.name .. " começa a formar o Ragnarok com alcance " .. range .. ". A área estará formada no próximo turno e custa " .. math.floor(mp / 2) .. " MP por turno.")
                    LUA,
            ],
            "Fundamentos gerais - Água" => [
                "Toque molhado" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque molhado"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque molhado.")
                      return
                    end
                    rpg.damage(1, mp)
                    rpg.applyEffect(1, "Molhado", nil, mp)
                    local healing = math.floor(mp / 2)
                    -- TODO: heal the character closest to the user (within 6 squares) by ½ of the damage dealt; needs positions to find them
                    rpg.log(target.name .. " recebe " .. mp .. " de dano e fica Molhado por " .. mp .. " turnos. O personagem mais próximo de " .. user.name .. " (até 6 quadrados) deve ser curado em " .. healing .. ".")
                    LUA,
                "Remover efeito" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of 6 squares; needs positions
                    local roll = rpg.roll((user.stats.magic or 0) + mp, "Remover efeito")
                    local turns = roll.hits
                    local removed = {}
                    local reduced = {}
                    for name, effect in pairs(target.effects) do
                      if name ~= "Voo" and name ~= "Espírito" then
                        if effect.duration ~= nil and effect.duration <= turns then
                          rpg.removeEffect(1, name)
                          table.insert(removed, name)
                        else
                          table.insert(reduced, name)
                        end
                      end
                    end
                    -- TODO: shortening an effect's duration without removing it needs an API to set remaining duration; effects not removed must be shortened by the GM
                    local message = turns .. " turno(s) removidos dos efeitos de " .. target.name .. "."
                    if #removed > 0 then
                      message = message .. " Removidos: " .. table.concat(removed, ", ") .. "."
                    end
                    if #reduced > 0 and turns > 0 then
                      message = message .. " Reduzir manualmente em " .. turns .. " turno(s): " .. table.concat(reduced, ", ") .. "."
                    end
                    rpg.log(message)
                    LUA,
                "Benção da deusa do lago" => <<<'LUA'
                    local ctx = ...
                    local cost = 1
                    if (ctx.input.cost or 0) ~= cost then
                      rpg.log("Aviso: Benção da deusa do lago custa " .. cost .. " MP por turno, mas foram usados " .. (ctx.input.cost or 0) .. ".")
                    end
                    local target = ctx.targets[1]
                    local who = "user"
                    local name = ctx.user.name
                    if target then
                      who = 1
                      name = target.name
                    end
                    rpg.applyEffect(who, "Flutuante")
                    -- TODO: upkeep of 1 MP per turn per affected character; needs per-turn upkeep
                    -- TODO: free action usable at any moment of the user's turn (or instantly when thrown into water); cancelled if an ally leaves a 15-square radius; needs action economy/positions
                    rpg.log(name .. " pode caminhar sobre as águas.")
                    LUA,
                "Vida líquida" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if #ctx.targets == 0 then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of 6 squares and bounces to characters within 1 square, healing the same character at most twice; needs positions
                    local healing = rpg.roll(math.floor(mp / 2), "Vida líquida").hits
                    for i, t in ipairs(ctx.targets) do
                      local amount = math.max(0, math.min(healing, t.maxAttributes.max_health - t.attributes.health))
                      rpg.heal(i, amount)
                      rpg.applyEffect(i, "Molhado", nil, 1)
                      rpg.log(t.name .. " é curado em " .. amount .. " e fica Molhado por 1 turno.")
                    end
                    LUA,
                "Onda" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    -- TODO: can only be cast on water; wave width MP travelling Magia squares; characters at the wave's edges are not knocked down; needs terrain/positions
                    if #ctx.targets == 0 then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    for i, t in ipairs(ctx.targets) do
                      rpg.applyEffect(i, "Derrubado")
                      rpg.applyEffect(i, "Molhado", nil, mp)
                      rpg.log(t.name .. " é Derrubado e fica Molhado por " .. mp .. " turnos.")
                    end
                    rpg.log("A onda tem largura " .. mp .. " e percorre " .. (user.stats.magic or 0) .. " quadrados.")
                    LUA,
                "Bolha" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of Magia squares; needs positions
                    -- TODO: the damage/heal choice needs a cast option input; reads ctx.input.mode ("heal") and defaults to damage
                    local healMode = ctx.input.mode == "heal"
                    local intelligence = user.stats.intelligence or 0
                    if not healMode then
                      local attack = rpg.contest(
                        (user.skills.ranged or 0) + (user.stats.magic or 0),
                        (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                        "Bolha"
                      )
                      if not attack.success then
                        rpg.log(target.name .. " desviou da Bolha.")
                        return
                      end
                    end
                    rpg.applyEffect(1, "Atordoado", nil, intelligence)
                    rpg.applyEffect(1, "Intangível", nil, intelligence)
                    rpg.applyEffect(1, "Molhado", nil, 2 * intelligence)
                    if healMode then
                      local healing = rpg.roll(math.floor(mp / 2), "Cura da Bolha").hits
                      local amount = math.max(0, math.min(healing, target.maxAttributes.max_health - target.attributes.health))
                      rpg.heal(1, amount)
                      rpg.log(target.name .. " é envolto pela bolha por " .. intelligence .. " turnos e curado em " .. amount .. ".")
                    else
                      local damage = math.floor(mp / 2)
                      rpg.damage(1, damage)
                      -- TODO: the target should take ½ MP damage every turn inside the bubble; only the first turn is applied now
                      rpg.log(target.name .. " é envolto pela bolha por " .. intelligence .. " turnos e recebe " .. damage .. " de dano por turno.")
                    end
                    LUA,
            ],
            "Fundamentos gerais - Ar" => [
                "Toque de vento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque de vento"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque de vento.")
                      return
                    end
                    rpg.damage(1, mp)
                    -- TODO: push the target 1 square back; needs positions
                    local message = target.name .. " recebe " .. mp .. " de dano e é empurrado 1 quadrado."
                    local tenacity = rpg.check((target.skills.tenacity or 0) + (target.stats.endurance or 0), mp, "Tenacidade de " .. target.name)
                    if not tenacity.success then
                      rpg.applyEffect(1, "Derrubado")
                      message = message .. " Falhou na Tenacidade e é Derrubado."
                    end
                    rpg.log(message)
                    LUA,
                "Vento invisível" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local duration = 2 * mp
                    -- TODO: the effect belongs to the weapon (enemies get -2 dice to dodge it); needs item effects and dodge modifiers
                    rpg.applyEffect("user", "Invisível", 2, duration)
                    rpg.log("A arma de " .. ctx.user.name .. " fica invisível por " .. duration .. " turnos: inimigos têm -2 dados para esquivar dela.")
                    LUA,
                "Avanço" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = "user"
                    local name = ctx.user.name
                    if target then
                      who = 1
                      name = target.name
                    end
                    -- TODO: free action before the movement phase, once per turn, range Magia*2 squares; needs action economy/positions
                    rpg.modify(who, "movement", mp)
                    rpg.log(name .. " recebe +" .. mp .. " Pontos de movimento.")
                    LUA,
                "Voo" => <<<'LUA'
                    local ctx = ...
                    local cost = 2
                    if (ctx.input.cost or 0) ~= cost then
                      rpg.log("Aviso: Voo custa " .. cost .. " MP por turno, mas foram usados " .. (ctx.input.cost or 0) .. ".")
                    end
                    local target = ctx.targets[1]
                    local who = "user"
                    local name = ctx.user.name
                    if target then
                      who = 1
                      name = target.name
                    end
                    rpg.applyEffect(who, "Voo")
                    -- TODO: upkeep of 2 MP per turn; free action once per turn; range Magia*2 squares, Voo lost when the target leaves it; needs upkeep/positions
                    rpg.log(name .. " recebe o efeito Voo.")
                    LUA,
                "Ventania" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: push the target MP squares; on hitting an object: ½ MP damage + Tenacidade check vs MP or Derrubado; on hitting a character: that character dodges with Acrobacia vs MP*2 - squares travelled, else ½ MP damage + Tenacidade vs MP or Derrubado; controlled mode never knocks down; needs positions/collision and a cast option
                    rpg.log(target.name .. " é empurrado " .. mp .. " quadrados. Em caso de colisão: " .. math.floor(mp / 2) .. " de dano e checagem de Tenacidade contra " .. mp .. " (mestre resolve).")
                    LUA,
                "Singularidade" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    rpg.applyEffect("user", "Concentrado")
                    -- TODO: channel one turn, then create a singularity (range 15, radius 10) pulling everyone 2*MP squares per turn; resisted with Resistência+Força vs Magia+MP (success: move up to ½ total movement); 1 damage per turn at the center +1 per other character there; needs channeling, positions and per-turn triggers
                    rpg.log(ctx.user.name .. " canaliza a Singularidade. Ela será criada no próximo turno, puxando " .. (2 * mp) .. " quadrados por turno.")
                    LUA,
            ],
            "Fundamentos gerais - Terra" => [
                "Duro como pedra" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Duro como pedra"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do ataque.")
                      return
                    end
                    rpg.damage(1, mp)
                    local message = target.name .. " recebe " .. mp .. " de dano."
                    local threshold = 2 * attack.opposingHits
                    local stunTurns = attack.hits - threshold
                    if stunTurns > 0 then
                      rpg.applyEffect(1, "Atordoado", nil, stunTurns)
                      message = message .. " Fica Atordoado por " .. stunTurns .. " turnos."
                    end
                    rpg.log(message)
                    LUA,
                "Qué ota" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of 10 squares; needs positions
                    local dodgePool = (target.skills.acrobatics or 0) + (target.stats.agility or 0) - math.floor(mp / 2)
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      math.max(dodgePool, 0),
                      "Qué ota"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou da pedra.")
                      return
                    end
                    rpg.damage(1, mp)
                    rpg.log(target.name .. " leva uma pedrada de " .. mp .. " de dano.")
                    LUA,
                "Fortificar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: Resistência (endurance stat) should increase by MP; needs stat modifiers, tracked with the Fortificado effect for now
                    rpg.applyEffect("user", "Fortificado", mp)
                    -- TODO: upkeep of ½ cast MP per turn; needs per-turn upkeep
                    rpg.log(ctx.user.name .. " tem a Resistência aumentada em " .. mp .. ". Manutenção: " .. math.floor(mp / 2) .. " MP por turno.")
                    LUA,
                "Barreira" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = "user"
                    local name = ctx.user.name
                    if target then
                      who = 1
                      name = target.name
                    end
                    -- TODO: ally must be within Magia squares; free action usable out of turn, once per round; needs positions/action economy
                    -- TODO: the wall should roll MP dice against the incoming attack before the dodge and reduce damage by 1 even on failure; needs a reaction/defense hook, tracked with the Protegido effect for now
                    rpg.applyEffect(who, "Protegido", mp, 1)
                    rpg.log("Uma barreira de pedra se ergue diante de " .. name .. " com " .. mp .. " dados de defesa.")
                    LUA,
                "Terraformar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local length = ctx.user.stats.magic or 0
                    -- TODO: create a destructible wall (length up to Magia, 3 HP per square, lasts MP turns) and push enemies on its path aside; needs map objects/positions
                    rpg.log(ctx.user.name .. " ergue uma parede de até " .. length .. " quadrados que dura " .. mp .. " turnos (3 de vida por quadrado).")
                    LUA,
                "Tumba" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of 6 squares; needs positions
                    local attack = rpg.contest(
                      (user.stats.magic or 0) + mp,
                      (target.stats.endurance or 0) + (target.stats.strength or 0),
                      "Tumba"
                    )
                    if not attack.success or attack.hits < 1 then
                      rpg.log(target.name .. " resistiu à Tumba.")
                      return
                    end
                    local hits = attack.hits
                    local depth
                    if hits >= 9 then
                      depth = "completamente enterrado: não pode se mover, atacar, desviar nem falar, e recebe 1 de dano por turno"
                    elseif hits >= 7 then
                      depth = "enterrado até os ombros: não pode se mover, atacar nem desviar"
                    elseif hits >= 5 then
                      depth = "enterrado até os cotovelos: não pode andar nem atacar, esquiva apenas com Agilidade"
                    elseif hits >= 3 then
                      depth = "com as pernas enterradas: não pode se mover, esquiva apenas com Agilidade"
                    else
                      depth = "com os pés enterrados: não pode se mover"
                    end
                    rpg.applyEffect(1, "Enterrado", hits)
                    rpg.applyEffect("user", "Concentrado")
                    -- TODO: each turn the target rolls Força and each hit lowers the burial level by 1; at 9+ it takes 1 damage per turn; movement/attack/dodge restrictions per level need rule hooks
                    rpg.log(target.name .. " está " .. depth .. " (" .. hits .. " acertos).")
                    LUA,
            ],
            "Fundamentos gerais - Arcana" => [
                "Toque arcano" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque arcano"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque arcano.")
                      return
                    end
                    local duration = user.stats.magic or 0
                    rpg.damage(1, mp)
                    rpg.applyEffect(1, "Predestinado", nil, duration)
                    rpg.log(target.name .. " recebe " .. mp .. " de dano e fica Predestinado por " .. duration .. " turnos.")
                    LUA,
                "Esfera arcana" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local magic = user.stats.magic or 0
                    local damage = math.max(mp - 1, 0)
                    -- TODO: the sphere stays at its square (range Magia) for Inteligência turns and hits anyone on it at the start of the caster's turn for the same damage; needs map objects/positions/per-turn triggers
                    rpg.log("Esfera arcana criada (dano " .. damage .. "), permanece por " .. (user.stats.intelligence or 0) .. " turnos.")
                    if not target then
                      rpg.log("Nenhum alvo no local da esfera.")
                      return
                    end
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + magic,
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Esfera arcana"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou da Esfera arcana.")
                      return
                    end
                    rpg.damage(1, damage)
                    rpg.applyEffect(1, "Predestinado", nil, magic)
                    rpg.log(target.name .. " recebe " .. damage .. " de dano e fica Predestinado por " .. magic .. " turnos.")
                    LUA,
                "Manipulação arcana" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    -- TODO: requires an existing Esfera arcana of the user within Magia squares and moves it up to ½ MP squares inside that radius; needs persistent sphere objects/positions
                    rpg.log("Esfera arcana movida até " .. math.floor(mp / 2) .. " quadrados.")
                    if #ctx.targets == 0 then
                      rpg.log("Nenhum alvo atingido.")
                      return
                    end
                    for i, t in ipairs(ctx.targets) do
                      local attack = rpg.contest(
                        (user.skills.ranged or 0) + magic,
                        (t.skills.acrobatics or 0) + (t.stats.agility or 0),
                        "Manipulação arcana contra " .. t.name
                      )
                      if attack.success then
                        rpg.damage(i, magic)
                        rpg.applyEffect(i, "Predestinado", nil, magic)
                        rpg.log(t.name .. " recebe " .. magic .. " de dano e fica Predestinado por " .. magic .. " turnos.")
                      else
                        rpg.log(t.name .. " desviou da Esfera arcana.")
                      end
                    end
                    LUA,
                "Sobrecarga arcana" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    -- TODO: requires an existing Esfera arcana of the user, destroys it, and the explosion radius is its remaining turns + 2; needs persistent sphere objects/positions
                    if #ctx.targets == 0 then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    local attackPool = (user.skills.ranged or 0) + mp
                    for i, t in ipairs(ctx.targets) do
                      local dodge = rpg.contest(
                        (t.skills.acrobatics or 0) + (t.stats.agility or 0),
                        attackPool,
                        "Esquiva de " .. t.name
                      )
                      if dodge.success then
                        rpg.log(t.name .. " desviou completamente da explosão.")
                      else
                        rpg.damage(i, mp)
                        rpg.applyEffect(i, "Predestinado", nil, magic)
                        rpg.log(t.name .. " recebe " .. mp .. " de dano e fica Predestinado por " .. magic .. " turnos.")
                      end
                    end
                    LUA,
                "Cofre arcano" => <<<'LUA'
                    local ctx = ...
                    local magic = ctx.user.stats.magic or 0
                    -- TODO: store up to ⅓ Magia Esferas arcanas around the user (double remaining duration while stored), fire one or more at once (first full damage, others ½ on the same target, each costs ⅓ of its own MP); needs persistent sphere objects and a fire option
                    rpg.log(ctx.user.name .. " guarda Esferas arcanas no Cofre arcano (máximo " .. math.floor(magic / 3) .. ").")
                    LUA,
                "Poder ilimitado" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local magic = ctx.user.stats.magic or 0
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    -- TODO: on the turn after channeling the user gets ½ MP extra actions, and Predestinado can be re-applied after being consumed; needs channeling/action economy
                    -- TODO: limited to ⅓ Magia uses per day; needs a daily usage counter
                    rpg.log(ctx.user.name .. " canaliza o Poder ilimitado: no próximo turno terá " .. math.floor(mp / 2) .. " ações extras. Limite de " .. math.floor(magic / 3) .. " usos por dia.")
                    LUA,
            ],
            "Fundamentos gerais - Vazio" => [
                "Toque vazio" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: recoil damage of this spell should be doubled; needs the recoil amount or a recoil multiplier on the spell
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque vazio"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque vazio.")
                      return
                    end
                    local damage = mp + 1
                    rpg.damage(1, damage)
                    rpg.log(target.name .. " recebe " .. damage .. " de dano verdadeiro.")
                    LUA,
                "Dardo vazio" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: recoil damage of this spell should be doubled; needs the recoil amount or a recoil multiplier on the spell
                    -- TODO: range of Magia squares; needs positions
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Dardo vazio"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Dardo vazio.")
                      return
                    end
                    rpg.damage(1, mp)
                    rpg.log(target.name .. " recebe " .. mp .. " de dano verdadeiro.")
                    LUA,
                "Imprudência" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    -- TODO: needs its own input for the amount to convert; the cast cost is used as the MP to gain and is refunded here, and should not roll recoil
                    local health = math.ceil(mp / 3)
                    if health >= (user.attributes.health or 0) then
                      rpg.modify("user", "mana", mp)
                      rpg.log(user.name .. " não tem vida suficiente para converter " .. health .. " HP. MP devolvido.")
                      return
                    end
                    rpg.damage("user", health)
                    rpg.modify("user", "mana", 2 * mp)
                    -- TODO: free action usable at any moment of the user's turn; needs action economy
                    rpg.log(user.name .. " converte " .. health .. " HP em " .. mp .. " MP.")
                    LUA,
                "Último suspiro" => <<<'LUA'
                    local ctx = ...
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    local damage = math.max(0, user.maxAttributes.max_health - user.attributes.health)
                    local cost = math.floor(damage / 2)
                    if (ctx.input.cost or 0) ~= cost then
                      rpg.log("Aviso: Último suspiro custa " .. cost .. " MP (½ do dano), mas foram usados " .. (ctx.input.cost or 0) .. ".")
                    end
                    -- TODO: range of Magia squares; needs positions
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Último suspiro"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Último suspiro.")
                      return
                    end
                    rpg.damage(1, damage)
                    rpg.log(target.name .. " recebe " .. damage .. " de dano verdadeiro.")
                    LUA,
                "Caminho do vazio" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: recoil damage of this spell should be doubled; needs the recoil amount or a recoil multiplier on the spell
                    rpg.applyEffect("user", "Banido", nil, 1)
                    -- TODO: when Banido ends, a character on the user's square takes MP damage and is pushed 1 square (wall behind: Atordoado 1 turn + 1 extra damage); needs an on-expire trigger and positions
                    rpg.log(ctx.user.name .. " fica Banido até o início do próximo turno, mas ainda pode se mover. Ao sair, causa " .. mp .. " de dano verdadeiro a quem estiver no mesmo quadrado.")
                    LUA,
                "Supernova" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local damage = math.floor((user.stats.magic or 0) / 2)
                    local turns = math.floor(mp / 2)
                    if turns < 1 then
                      rpg.log("Supernova não tem duração com " .. mp .. " MP.")
                      return
                    end
                    -- TODO: range of Magia around the user; needs positions to find everyone affected
                    local total = 0
                    rpg.damage("user", damage)
                    total = total + damage
                    for i, t in ipairs(ctx.targets) do
                      rpg.damage(i, damage)
                      total = total + damage
                    end
                    -- TODO: repeat this damage every turn for ½ MP turns, then deal the accumulated total to everyone in range; needs per-turn triggers and a counter
                    rpg.log("Supernova: todos no alcance, incluindo " .. user.name .. ", recebem " .. damage .. " de dano verdadeiro por " .. turns .. " turnos (" .. total .. " neste turno). Ao final, todos recebem o total acumulado.")
                    LUA,
            ],
            "Fundamentos gerais - Eletricidade" => [
                "Toque chocante" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque chocante"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque chocante.")
                      return
                    end
                    -- TODO: ricochet to everyone adjacent (and their adjacents), each hit once, excluding the caster; needs positions; extra targets are treated as the chain
                    for i, t in ipairs(ctx.targets) do
                      rpg.damage(i, mp)
                      rpg.log(t.name .. " recebe " .. mp .. " de dano.")
                    end
                    LUA,
                "Orbe elétrico" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of Magia squares; needs positions
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Orbe elétrico"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Orbe elétrico.")
                      return
                    end
                    -- TODO: also hits everyone within 2 squares and ricochets to their adjacents, each hit once; needs positions; extra targets are treated as the chain
                    for i, t in ipairs(ctx.targets) do
                      rpg.damage(i, mp)
                      rpg.log(t.name .. " recebe " .. mp .. " de dano.")
                    end
                    LUA,
                "Avanço" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local limit = (user.stats.agility or 0) + (user.stats.magic or 0)
                    local distance = math.min(mp, limit)
                    -- TODO: move the user instantly up to the distance and hit everyone on the path, ricocheting to their adjacents (each hit once); needs positions; selected targets are treated as hit
                    rpg.log(user.name .. " avança instantaneamente " .. distance .. " quadrados.")
                    for i, t in ipairs(ctx.targets) do
                      rpg.damage(i, mp)
                      rpg.log(t.name .. " recebe " .. mp .. " de dano.")
                    end
                    LUA,
                "Descarga" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local radius = ctx.user.stats.intelligence or 0
                    -- TODO: hits everyone within Inteligência squares and ricochets to their adjacents (each hit once); needs positions; selected targets are treated as hit
                    rpg.log(ctx.user.name .. " libera uma descarga com alcance " .. radius .. ".")
                    if #ctx.targets == 0 then
                      rpg.log("Nenhum alvo atingido.")
                      return
                    end
                    for i, t in ipairs(ctx.targets) do
                      rpg.damage(i, mp)
                      rpg.log(t.name .. " recebe " .. mp .. " de dano.")
                    end
                    LUA,
                "Trovoada" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: square chosen within Magia range; other targets within Inteligência radius plus ricochet to adjacents (each hit once); needs positions; target 1 is the struck square, the rest are the area/chain
                    for i, t in ipairs(ctx.targets) do
                      local damage = mp
                      if i == 1 then
                        damage = 2 * mp
                      end
                      rpg.damage(i, damage)
                      rpg.log(t.name .. " recebe " .. damage .. " de dano.")
                    end
                    -- TODO: optional teleport of the user to the struck square (or an adjacent one if occupied); needs positions
                    rpg.log("Um raio cai no ponto escolhido. " .. ctx.user.name .. " pode se teletransportar para lá.")
                    LUA,
                "Tempestade" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local size = (user.stats.intelligence or 0) + (user.stats.magic or 0)
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    -- TODO: after 1 turn of channeling, create a storm of area Inteligência+Magia lasting MP turns: electricity spells inside cost no MP and have ½ recoil; once per turn roll 1 die and on a hit strike a chosen target for MP damage with ricochet; needs channeling, area, cost/recoil modifiers and per-turn triggers
                    rpg.log(user.name .. " acumula energia. No próximo turno, uma tempestade de área " .. size .. " surgirá por " .. mp .. " turnos.")
                    LUA,
            ],
            "Fundamentos gerais - Gelo" => [
                "Toque congelante" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: should trigger on the user's next attack with the infused body part; resolved immediately for now
                    local attack = rpg.contest(
                      (user.skills.melee or 0) + (user.stats.strength or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Toque congelante"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Toque congelante.")
                      return
                    end
                    rpg.damage(1, mp)
                    local frozen = target.effects["Congelado"]
                    if frozen and (frozen.power or 0) >= 3 then
                      local stunTurns = math.floor((frozen.duration or 0) / 3)
                      rpg.removeEffect(1, "Congelado")
                      if stunTurns > 0 then
                        rpg.applyEffect(1, "Atordoado", nil, stunTurns)
                      end
                      rpg.log(target.name .. " recebe " .. mp .. " de dano e o Congelado vira Atordoado por " .. stunTurns .. " turnos.")
                      return
                    end
                    rpg.applyEffect(1, "Congelado", mp, mp)
                    rpg.log(target.name .. " recebe " .. mp .. " de dano e é Congelado em " .. mp .. " por " .. mp .. " turnos.")
                    LUA,
                "Dardo de gelo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range of Magia squares; needs positions
                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Dardo de gelo"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " desviou do Dardo de gelo.")
                      return
                    end
                    rpg.damage(1, mp)
                    local frozen = target.effects["Congelado"]
                    if frozen and (frozen.power or 0) >= 3 then
                      local stunTurns = math.floor((frozen.duration or 0) / 3)
                      rpg.removeEffect(1, "Congelado")
                      if stunTurns > 0 then
                        rpg.applyEffect(1, "Atordoado", nil, stunTurns)
                      end
                      rpg.log(target.name .. " recebe " .. mp .. " de dano e o Congelado vira Atordoado por " .. stunTurns .. " turnos.")
                      return
                    end
                    rpg.applyEffect(1, "Congelado", mp, mp)
                    rpg.log(target.name .. " recebe " .. mp .. " de dano e é Congelado em " .. mp .. ".")
                    LUA,
                "Bloco de gelo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = "user"
                    local name = ctx.user.name
                    if target then
                      who = 1
                      name = target.name
                    end
                    -- TODO: range of Magia squares; needs positions
                    local duration = math.floor(mp / 2)
                    rpg.applyEffect(who, "Atordoado", nil, duration)
                    rpg.applyEffect(who, "Intangível", nil, duration)
                    rpg.log(name .. " fica preso em um bloco de gelo: Atordoado e Intangível por " .. duration .. " turnos.")
                    LUA,
                "Piso congelado" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local magic = ctx.user.stats.magic or 0
                    -- TODO: create ice (roll 1 die when moving through: miss = Derrubado, hit = movement point refunded) or snow (+1 movement cost) on up to Magia adjacent squares within Magia*2, lasting MP turns; needs terrain/positions and a cast option
                    rpg.log(ctx.user.name .. " congela até " .. magic .. " quadrados por " .. mp .. " turnos.")
                    LUA,
                "Nevasca" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local half = math.floor(mp / 2)
                    local size = math.floor((ctx.user.stats.magic or 0) / 2)
                    -- TODO: the storm (size ½ Magia) follows the user for MP turns and affects everyone inside each turn; needs area/positions and per-turn triggers; applied once to the selected targets
                    rpg.log(ctx.user.name .. " cria uma nevasca de tamanho " .. size .. " por " .. mp .. " turnos.")
                    for i, t in ipairs(ctx.targets) do
                      local frozen = t.effects["Congelado"]
                      if frozen then
                        local power = math.max(frozen.power or 0, half)
                        local duration = math.max(frozen.duration or 0, half)
                        rpg.applyEffect(i, "Congelado", power, duration)
                        rpg.damage(i, 1)
                        rpg.log(t.name .. " já estava Congelado: duração renovada para " .. duration .. " turnos e recebe 1 de dano verdadeiro.")
                      else
                        rpg.applyEffect(i, "Congelado", half, half)
                        rpg.log(t.name .. " é Congelado em " .. half .. ".")
                      end
                    end
                    LUA,
                "Patinação" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    -- TODO: after 1 turn of channeling, create an ice area of radius 2*MP: anyone moving rolls 1 die (miss = Derrubado); user and allies get +Magia movement; everyone inside is Congelado MP, renewed each turn; upkeep ½ MP per turn; needs channeling, area/positions, per-turn triggers and upkeep
                    rpg.log(user.name .. " canaliza a Patinação: no próximo turno, uma área de raio " .. (2 * mp) .. " será congelada. Manutenção: " .. math.floor(mp / 2) .. " MP por turno.")
                    LUA,
            ],
        ];
    }

    /**
     * Mineralogia, Numerologia, Astromancia, Criação, Botânica, Evocação espiritual, Necromancia and Zoologia.
     *
     * @return array<string, array<string, string>>
     */
    private function gemNumberStarCraftStrategies(): array
    {
        return [
            "Mineralogia" => [
                "Transferência" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: gems are not modeled as items; storing MP needs a gem item holding the stored MP, capped by the gem's quality.
                    rpg.log(ctx.user.name .. " armazenou " .. mp .. " MP em uma gema. Ao usar a gema, todo o MP armazenado é gasto de uma vez.")
                    LUA,
                "Liberação" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: gems are not modeled as items; the damage should come from the MP stored in a chosen gem (and destroy it) instead of the cast cost.
                    -- TODO: range (Magia + Combate à distância squares) is not validated.
                    local pool = (user.skills.ranged or 0) + (user.stats.magic or 0)
                    local dodge = (target.skills.acrobatics or 0) + (target.stats.agility or 0)
                    local attack = rpg.contest(pool, dodge, "Liberação")
                    if attack.success then
                      rpg.damage(1, mp)
                      rpg.log(target.name .. " foi atingido pela explosão da gema e recebeu " .. mp .. " de dano.")
                    else
                      rpg.log(target.name .. " esquivou da explosão da gema.")
                    end
                    rpg.log("A gema foi destruída.")
                    LUA,
                "Estourar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    if not ctx.targets[1] then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: gems are not modeled as items; MP should come from the gem's stored MP instead of the cast cost.
                    -- TODO: the cone (length Magia, widening every 2 squares) and blocking by objects/targets in front need positions; the selected targets are treated as the ones in the cone.
                    local pool = (user.skills.ranged or 0) + (user.stats.magic or 0)
                    local damage = math.floor(mp / 2)
                    for i, t in ipairs(ctx.targets) do
                      local dodge = (t.skills.acrobatics or 0) + (t.stats.agility or 0)
                      local attack = rpg.contest(pool, dodge, "Estourar em " .. t.name)
                      if attack.success then
                        rpg.damage(i, damage)
                        rpg.log(t.name .. " foi atingido pelo estouro e recebeu " .. damage .. " de dano.")
                      else
                        rpg.log(t.name .. " esquivou do estouro.")
                      end
                    end
                    rpg.log("A gema foi destruída.")
                    LUA,
                "Atirar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    if not ctx.targets[1] then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: gems are not modeled as items; MP should come from the gem's stored MP instead of the cast cost.
                    -- TODO: the piercing straight line (range 3 * (Magia + Combate à distância)) needs positions; the selected targets are treated as the ones in its path.
                    local pool = (user.skills.ranged or 0) + (user.stats.magic or 0)
                    local damage = math.floor(mp / 2)
                    for i, t in ipairs(ctx.targets) do
                      local dodge = (t.skills.acrobatics or 0) + (t.stats.agility or 0)
                      local attack = rpg.contest(pool, dodge, "Atirar em " .. t.name)
                      if attack.success then
                        rpg.damage(i, damage)
                        rpg.log(t.name .. " foi perfurado pelo projétil e recebeu " .. damage .. " de dano.")
                      else
                        rpg.log(t.name .. " esquivou do projétil.")
                      end
                    end
                    rpg.log("A gema foi destruída.")
                    LUA,
                "Liberação Múltipla" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: gems are not modeled as items and the number of gems is not an input; ctx.input.gems is used when present, otherwise 1 gem is assumed and the cost is the total stored MP.
                    local gems = math.max(1, math.floor(ctx.input.gems or 1))
                    local damage = math.max(0, mp - gems)
                    local pool = (user.skills.ranged or 0) + (user.stats.magic or 0)
                    local dodge = (target.skills.acrobatics or 0) + (target.stats.agility or 0)
                    local attack = rpg.contest(pool, dodge, "Liberação Múltipla")
                    if attack.success then
                      rpg.damage(1, damage)
                      rpg.log(target.name .. " foi atingido por " .. gems .. " liberação(ões) e recebeu " .. damage .. " de dano.")
                    else
                      rpg.log(target.name .. " esquivou das liberações.")
                    end
                    rpg.log(gems .. " gema(s) destruída(s).")
                    LUA,
                "Armazenamento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: gems and stored spells are not modeled; needs a gem item that holds a spell and casts it (breaking the gem) as a normal action without recoil.
                    rpg.log(ctx.user.name .. " armazenou uma magia de " .. mp .. " MP em uma gema. O coice já foi aplicado no armazenamento.")
                    LUA,
            ],
            "Numerologia" => [
                "Previsão" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 5 then
                      rpg.log("Aviso: Previsão custa 5 MP, mas foram usados " .. mp .. " MP.")
                    end
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local name = target and target.name or ctx.user.name
                    -- TODO: no next-roll trigger and no input for the prediction or for adding vs removing; the GM must apply +5 or -5 dice to the next relevant roll.
                    rpg.applyEffect(who, "Previsto", 5)
                    rpg.log(ctx.user.name .. " fez uma previsão envolvendo " .. name .. ": a próxima rolagem relevante ganha ou perde 5 dados.")
                    LUA,
                "Equacionar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: previous roll results are not accessible; the copied result must be picked by the player (cost = copied hits).
                    -- TODO: pasting the hits into a similar action (free action, even off-turn, optional for other characters) needs a roll-override hook.
                    rpg.applyEffect("user", "Equacionado", mp)
                    rpg.log(ctx.user.name .. " copiou um resultado de " .. mp .. " acerto(s), que pode ser colado em uma ação similar.")
                    LUA,
                "Reescrever" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 10 then
                      rpg.log("Aviso: Reescrever custa 10 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: game-rule changes are GM-handled; needs upkeep of 4 MP per turn and 5 damage to the caster each time a flawed rule has to be compensated.
                    rpg.log(ctx.user.name .. " reescreveu uma mecânica do universo. Manutenção: 4 MP por turno; cada falha compensada causa 5 de dano ao usuário.")
                    LUA,
                "Viés" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 10 then
                      rpg.log("Aviso: Viés custa 10 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: the dice roller must count 3-6 as hits for the caster while Enviesado is active; needs upkeep of 5 MP per turn.
                    rpg.applyEffect("user", "Enviesado")
                    rpg.log(ctx.user.name .. " enviesou o universo: suas rolagens contam 3, 4, 5 e 6 como acertos. Manutenção: 5 MP por turno.")
                    LUA,
                "Teoria" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    if (user.attributes.health or 0) <= 0 then
                      rpg.log(user.name .. " não sobreviveu ao coice; a Teoria não entra em ação.")
                      return
                    end
                    -- TODO: the rewritten fact and its cost are GM discretion; nothing is applied automatically.
                    rpg.log(user.name .. " sobreviveu ao coice e reescreveu um fato do universo (" .. mp .. " MP). O GM resolve o efeito.")
                    LUA,
            ],
            "Astromancia" => [
                "Tiro de sorte" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    if not ctx.targets[1] then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: range (6 squares) and choosing the nearest character for each bounce need positions; bounces follow the selected targets in order and the GM resolves bounces past the last one (they may hit the caster).
                    local pool = (user.skills.ranged or 0) + (user.stats.magic or 0)
                    for i, t in ipairs(ctx.targets) do
                      local dodge = (t.skills.acrobatics or 0) + (t.stats.agility or 0)
                      local attack = rpg.contest(pool, dodge, "Tiro de sorte em " .. t.name)
                      if not attack.success then
                        rpg.log(t.name .. " esquivou do tiro de sorte.")
                        break
                      end
                      rpg.damage(i, mp)
                      rpg.log(t.name .. " foi atingido e recebeu " .. mp .. " de dano.")
                      local bounce = rpg.roll(1, "Rebote").rolls[1] or 1
                      if bounce % 2 ~= 0 then
                        rpg.log("O tiro não rebateu.")
                        break
                      end
                      if not ctx.targets[i + 1] then
                        rpg.log("O tiro rebate para o personagem mais próximo de " .. t.name .. " (até 6 quadrados).")
                      end
                    end
                    LUA,
                "Destino" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    local maxHealth = target.maxAttributes.max_health or 0
                    -- TODO: needs a death trigger (only if this target is the next to die), tracking of everyone who took part in the kill, a d8 stat roll per participant and temporary stat bonuses for MP turns.
                    rpg.applyEffect(1, "Destinado", mp)
                    rpg.log(target.name .. " foi marcado pelo Destino. Se for o próximo a morrer, os participantes dividem " .. maxHealth .. " de bônus em uma estatística aleatória (d8) por " .. mp .. " turnos.")
                    LUA,
                "Horóscopo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: the predictions are not inputs and there is no trigger 3 turns later; the GM resolves: right = deals MP damage, wrong = takes MP damage (2*MP if the other was right).
                    rpg.applyEffect("user", "Prognosticado", mp, 3)
                    rpg.applyEffect(1, "Prognosticado", mp, 3)
                    rpg.log(user.name .. " e " .. target.name .. " preveem a vida um do outro daqui a 3 turnos (vida atual: " .. (target.attributes.health or 0) .. " e " .. (user.attributes.health or 0) .. ", que não podem ser usadas como previsão).")
                    LUA,
                "Alinhamento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local name = target and target.name or ctx.user.name
                    -- TODO: no next-roll trigger; the GM must double the hits of the next roll if it equals the declared number (free action, can be cast off-turn).
                    rpg.applyEffect(who, "Alinhado", mp)
                    rpg.log(ctx.user.name .. " declarou " .. mp .. ": se a próxima rolagem de " .. name .. " tiver exatamente " .. mp .. " acerto(s), os acertos são dobrados.")
                    LUA,
                "Chuva de meteoros" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: the 9-square area needs positions; the caster plus the selected targets are treated as everyone in range.
                    local function meteor(who, sheet)
                      local die = rpg.roll(1, "Meteoro em " .. sheet.name).rolls[1] or 1
                      if die % 2 == 0 then
                        rpg.damage(who, mp)
                        rpg.log("Um meteoro caiu em " .. sheet.name .. ", causando " .. mp .. " de dano.")
                      else
                        rpg.log("Nenhum meteoro caiu em " .. sheet.name .. ".")
                      end
                    end
                    meteor("user", ctx.user)
                    for i, t in ipairs(ctx.targets) do
                      meteor(i, t)
                    end
                    LUA,
                "Roleta brasileira" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if mp ~= 10 then
                      rpg.log("Aviso: Roleta brasileira custa 10 MP, mas foram usados " .. mp .. " MP.")
                    end
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: only the die variant is implemented; the coin variant (including landing on its edge, which zeroes both) is not modeled.
                    local die = rpg.roll(1, "Roleta brasileira").rolls[1] or 1
                    if die % 2 == 0 then
                      rpg.damage("user", math.max(0, user.attributes.health or 0))
                      rpg.log("Cara! " .. user.name .. " teve sua vida reduzida a zero.")
                    else
                      rpg.damage(1, math.max(0, target.attributes.health or 0))
                      rpg.log("Coroa! " .. target.name .. " teve sua vida reduzida a zero.")
                    end
                    LUA,
            ],
            "Criação" => [
                "Copiar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 3 then
                      rpg.log("Aviso: Copiar custa 3 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: created objects are not items; needs a fragile copy item (-1 die on its rolls, breaks after first use, no special properties) and the first-copy-per-turn free action.
                    rpg.log(ctx.user.name .. " copiou um objeto em sua visão. A cópia é frágil: -1 dado nas rolagens e quebra após o primeiro uso.")
                    LUA,
                "Imaginar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 4 then
                      rpg.log("Aviso: Imaginar custa 4 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: created objects are not items; needs a fragile copy item of a simple imagined object.
                    rpg.log(ctx.user.name .. " criou a cópia de um objeto simples imaginado.")
                    LUA,
                "Armazenar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 5 and mp ~= 6 then
                      rpg.log("Aviso: Armazenar custa 5 MP (1 MP a mais para objetos criados), mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: needs a per-character memory of stored objects and copy items created from it.
                    rpg.log(ctx.user.name .. " criou uma cópia a partir de um objeto armazenado na memória.")
                    LUA,
                "Criar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local upkeep = math.ceil(mp / 4)
                    -- TODO: created objects are not items; needs a perfect copy item with the original's effects and an upkeep of ¼ of the cost (rounded up) per turn.
                    rpg.log(ctx.user.name .. " criou um objeto perfeito por " .. mp .. " MP. Manutenção: " .. upkeep .. " MP por turno.")
                    LUA,
            ],
            "Botânica" => [
                "Aprendiz de botânica" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; the daily potion limit (1 per day) is not tracked.
                    rpg.log(ctx.user.name .. " pode criar uma poção por dia.")
                    LUA,
                "Poção de melhoramento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local name = target and target.name or ctx.user.name
                    -- TODO: potions are not items (created and drunk at once here); the chosen general stat is not an input and stats cannot be modified (+1 for MP turns).
                    rpg.applyEffect(who, "Melhorado", 1, mp)
                    rpg.log(name .. " bebeu uma poção de melhoramento: +1 em uma estatística geral por " .. mp .. " turnos.")
                    LUA,
                "Formado em botânica" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; the daily potion limit (2 per day) is not tracked.
                    rpg.log(ctx.user.name .. " pode criar duas poções por dia.")
                    LUA,
                "Poção de vida" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local sheet = target or ctx.user
                    -- TODO: potions are not items; the potion is created and drunk at once here.
                    local hits = rpg.roll(mp, "Poção de vida").hits
                    local missing = (sheet.maxAttributes.max_health or 0) - (sheet.attributes.health or 0)
                    local amount = math.max(0, math.min(hits, missing))
                    rpg.heal(who, amount)
                    rpg.log(sheet.name .. " bebeu uma poção de vida e recuperou " .. amount .. " de vida.")
                    LUA,
                "Poção de enfraquecimento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: potions are not items; only the thrown use is implemented. Coating a weapon needs a next-attack trigger (melee keeps it until it hits, ranged loses it on a miss).
                    -- TODO: the chosen general stat is not an input and stats cannot be modified (-1 for MP turns).
                    local pool = (user.skills.ranged or 0) + (user.stats.dexterity or 0)
                    local dodge = (target.skills.acrobatics or 0) + (target.stats.agility or 0)
                    local attack = rpg.contest(pool, dodge, "Arremesso da poção")
                    if attack.success then
                      rpg.applyEffect(1, "Enfraquecido", 1, mp)
                      rpg.log(target.name .. " foi atingido pela poção e está Enfraquecido por " .. mp .. " turnos.")
                    else
                      rpg.log(target.name .. " esquivou da poção.")
                    end
                    LUA,
                "Mestre em botânica" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; the daily potion limit (3 per day) is not tracked.
                    rpg.log(ctx.user.name .. " pode criar três poções por dia.")
                    LUA,
                "Poção de fortalecimento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local name = target and target.name or ctx.user.name
                    -- TODO: potions are not items (created and drunk at once here); the chosen class stat is not an input and stats cannot be modified (+1 for MP turns).
                    rpg.applyEffect(who, "Fortalecido", 1, mp)
                    rpg.log(name .. " bebeu uma poção de fortalecimento: +1 na estatística de classe por " .. mp .. " turnos.")
                    LUA,
                "Poção de veneno" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    -- TODO: potions are not items; only the thrown use is implemented. Coating a weapon needs a next-attack trigger (melee keeps it until it hits, ranged loses it on a miss).
                    local pool = (user.skills.ranged or 0) + (user.stats.dexterity or 0)
                    local dodge = (target.skills.acrobatics or 0) + (target.stats.agility or 0)
                    local attack = rpg.contest(pool, dodge, "Arremesso da poção")
                    if attack.success then
                      rpg.applyEffect(1, "Envenenado", 1, mp)
                      rpg.log(target.name .. " foi Envenenado por " .. mp .. " turnos (1 dado por turno; acerto causa 1 de dano verdadeiro).")
                    else
                      rpg.log(target.name .. " esquivou da poção.")
                    end
                    LUA,
                "Grão-mestre em botânica" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; the daily potion limit (4 per day) is not tracked.
                    rpg.log(ctx.user.name .. " pode criar quatro poções por dia.")
                    LUA,
                "Poção de regeneração" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local name = target and target.name or ctx.user.name
                    local turns = mp + (ctx.user.stats.magic or 0)
                    -- TODO: potions are not items; the potion is created and drunk at once here.
                    rpg.applyEffect(who, "Regenerando", 1, turns)
                    rpg.log(name .. " bebeu uma poção de regeneração: por " .. turns .. " turnos rola 1 dado e, em caso de acerto, regenera 1 de vida.")
                    LUA,
                "Ancião em botânica" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; the daily potion limit (5 per day) is not tracked.
                    rpg.log(ctx.user.name .. " pode criar cinco poções por dia.")
                    LUA,
                "Poção de mana" => <<<'LUA'
                    local ctx = ...
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local sheet = target or ctx.user
                    local magic = ctx.user.stats.magic or 0
                    -- TODO: potions are not items; the potion is created and drunk at once here.
                    local cap = sheet.maxAttributes.max_mana or magic
                    local amount = math.max(0, math.min(magic, cap - (sheet.attributes.mana or 0)))
                    rpg.modify(who, "mana", amount)
                    rpg.log(sheet.name .. " bebeu uma poção de mana e recuperou " .. amount .. " MP.")
                    LUA,
            ],
            "Evocação espiritual" => [
                "Evocar pombo na coleira pássaro" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 1 then
                      rpg.log("Aviso: Evocar pássaro custa 1 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summons are not modeled; needs a spirit bird sheet and an upkeep of 1 MP per turn (dispersed when MP runs out).
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    rpg.log(ctx.user.name .. " passa o turno evocando um frágil pássaro espiritual. Manutenção: 1 MP por turno.")
                    LUA,
                "Evocar rato" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 1 then
                      rpg.log("Aviso: Evocar rato custa 1 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summons are not modeled; needs a spirit rat sheet and an upkeep of 1 MP per turn (dispersed when MP runs out).
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    rpg.log(ctx.user.name .. " passa o turno evocando um rato espiritual. Manutenção: 1 MP por turno.")
                    LUA,
                "Evocar lobo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 2 then
                      rpg.log("Aviso: Evocar lobo custa 2 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summons are not modeled; needs a spirit wolf sheet and an upkeep of 1 MP per turn (dispersed when MP runs out).
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    rpg.log(ctx.user.name .. " passa o turno evocando um lobo espiritual. Manutenção: 1 MP por turno.")
                    LUA,
                "Evocar espírito" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 3 then
                      rpg.log("Aviso: Evocar espírito custa 3 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summons are not modeled; needs the chosen named spirit's sheet, an upkeep of 2 MP per turn and a limit of one active evocation per spirit per caster.
                    rpg.applyEffect("user", "Concentrado", nil, 1)
                    rpg.log(ctx.user.name .. " passa o turno evocando um espírito nomeado. Manutenção: 2 MP por turno.")
                    LUA,
                "Evocar elemental" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if mp ~= 5 then
                      rpg.log("Aviso: Evocar elemental custa 5 MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: fire alignment only, but alignment is not in the sheet snapshot.
                    -- TODO: summons are not modeled; needs a fire elemental sheet, an upkeep of 5 MP per turn, and interruption (losing half the MP) if the caster is attacked during the 2-turn channel.
                    rpg.applyEffect("user", "Concentrado", nil, 2)
                    rpg.log(ctx.user.name .. " canaliza por 2 turnos para evocar um elemental de fogo. Se for atacado, a evocação é interrompida e metade do MP é perdida. Manutenção: 5 MP por turno.")
                    LUA,
                "Evocar doppelganger" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    if mp ~= magic then
                      rpg.log("Aviso: Evocar doppelganger custa MP igual à Magia (" .. magic .. "), mas foram usados " .. mp .. " MP.")
                    end
                    local upkeep = math.floor(magic / 2)
                    local health = math.min(user.attributes.health or 0, user.maxAttributes.max_health or 0)
                    local negative = { "Queimado", "Cauterizado", "Molhado", "Derrubado", "Atordoado", "Congelado", "Predestinado", "Banido", "Exausto", "Envenenado", "Enfraquecido" }
                    local shared = {}
                    for _, name in ipairs(negative) do
                      if (user.effects or {})[name] then
                        table.insert(shared, name)
                      end
                    end
                    -- TODO: summons are not modeled; needs a copy of the caster's sheet with every stat -1, the HP and negative effects below, an upkeep per turn, and interruption (losing half the MP) if the caster is attacked during the 2-turn channel.
                    rpg.applyEffect("user", "Concentrado", nil, 2)
                    local effects = #shared > 0 and table.concat(shared, ", ") or "nenhum"
                    rpg.log(user.name .. " canaliza por 2 turnos para evocar um doppelganger: estatísticas -1, " .. health .. " de vida, efeitos negativos compartilhados: " .. effects .. ". Manutenção: " .. upkeep .. " MP por turno.")
                    LUA,
            ],
            "Necromancia" => [
                "Aprendiz em necromancia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local penalty = 0
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    if (target.attributes.health or 0) > 0 then
                      rpg.log(target.name .. " não está morto.")
                      return
                    end
                    local statSum = 0
                    for _, value in pairs(target.stats) do
                      if type(value) == "number" then
                        statSum = statSum + value
                      end
                    end
                    local ritual = rpg.contest((user.stats.magic or 0) + mp, math.max(0, statSum - penalty), "Necromancia")
                    if ritual.diff <= 0 then
                      rpg.log("A reanimação de " .. target.name .. " falhou.")
                      return
                    end
                    local health = math.floor((target.maxAttributes.max_health or 0) / 2)
                    local turns = mp
                    rpg.heal(1, math.max(0, health - (target.attributes.health or 0)))
                    -- TODO: needs control of the revived enemy, -1 on all its stats (minimum 1) via stat modification, and its death after the duration.
                    rpg.applyEffect(1, "Reanimado", 1, turns)
                    rpg.log(target.name .. " foi reanimado com " .. health .. " de vida, -1 em todas as estatísticas, por " .. turns .. " turnos.")
                    LUA,
                "Formado em necromancia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local penalty = 2
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    if (target.attributes.health or 0) > 0 then
                      rpg.log(target.name .. " não está morto.")
                      return
                    end
                    local statSum = 0
                    for _, value in pairs(target.stats) do
                      if type(value) == "number" then
                        statSum = statSum + value
                      end
                    end
                    local ritual = rpg.contest((user.stats.magic or 0) + mp, math.max(0, statSum - penalty), "Necromancia")
                    if ritual.diff <= 0 then
                      rpg.log("A reanimação de " .. target.name .. " falhou.")
                      return
                    end
                    local health = math.floor((target.maxAttributes.max_health or 0) / 2)
                    local turns = mp
                    rpg.heal(1, math.max(0, health - (target.attributes.health or 0)))
                    -- TODO: needs control of the revived enemy, -1 on all its stats (minimum 1) via stat modification, and its death after the duration.
                    rpg.applyEffect(1, "Reanimado", 1, turns)
                    rpg.log(target.name .. " foi reanimado com " .. health .. " de vida, -1 em todas as estatísticas, por " .. turns .. " turnos.")
                    LUA,
                "Mestre em necromancia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local penalty = 4
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    if (target.attributes.health or 0) > 0 then
                      rpg.log(target.name .. " não está morto.")
                      return
                    end
                    local statSum = 0
                    for _, value in pairs(target.stats) do
                      if type(value) == "number" then
                        statSum = statSum + value
                      end
                    end
                    local ritual = rpg.contest((user.stats.magic or 0) + mp, math.max(0, statSum - penalty), "Necromancia")
                    if ritual.diff <= 0 then
                      rpg.log("A reanimação de " .. target.name .. " falhou.")
                      return
                    end
                    local health = math.floor((target.maxAttributes.max_health or 0) / 2)
                    local turns = mp + (user.stats.magic or 0)
                    rpg.heal(1, math.max(0, health - (target.attributes.health or 0)))
                    -- TODO: needs control of the revived enemy, -1 on all its stats (minimum 1) via stat modification, and its death after the duration.
                    rpg.applyEffect(1, "Reanimado", 1, turns)
                    rpg.log(target.name .. " foi reanimado com " .. health .. " de vida, -1 em todas as estatísticas, por " .. turns .. " turnos.")
                    LUA,
                "Grão-mestre em necromancia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local penalty = 6
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    if (target.attributes.health or 0) > 0 then
                      rpg.log(target.name .. " não está morto.")
                      return
                    end
                    local statSum = 0
                    for _, value in pairs(target.stats) do
                      if type(value) == "number" then
                        statSum = statSum + value
                      end
                    end
                    local ritual = rpg.contest((user.stats.magic or 0) + mp, math.max(0, statSum - penalty), "Necromancia")
                    if ritual.diff <= 0 then
                      rpg.log("A reanimação de " .. target.name .. " falhou.")
                      return
                    end
                    local health = target.maxAttributes.max_health or 0
                    local turns = mp + (user.stats.magic or 0)
                    rpg.heal(1, math.max(0, health - (target.attributes.health or 0)))
                    -- TODO: needs control of the revived enemy, -1 on all its stats (minimum 1) via stat modification, and its death after the duration.
                    rpg.applyEffect(1, "Reanimado", 1, turns)
                    rpg.log(target.name .. " foi reanimado com " .. health .. " de vida (vida máxima original), -1 em todas as estatísticas, por " .. turns .. " turnos.")
                    LUA,
                "Ancião em necromancia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local penalty = 8
                    if not target then
                      rpg.log("Nenhum alvo selecionado.")
                      return
                    end
                    if (target.attributes.health or 0) > 0 then
                      rpg.log(target.name .. " não está morto.")
                      return
                    end
                    local statSum = 0
                    for _, value in pairs(target.stats) do
                      if type(value) == "number" then
                        statSum = statSum + value
                      end
                    end
                    local ritual = rpg.contest((user.stats.magic or 0) + mp, math.max(0, statSum - penalty), "Necromancia")
                    if ritual.diff <= 0 then
                      rpg.log("A reanimação de " .. target.name .. " falhou.")
                      return
                    end
                    local health = target.maxAttributes.max_health or 0
                    local turns = mp + (user.stats.magic or 0)
                    rpg.heal(1, math.max(0, health - (target.attributes.health or 0)))
                    -- TODO: needs control of the revived enemy and its death after the duration.
                    rpg.applyEffect(1, "Reanimado", nil, turns)
                    rpg.log(target.name .. " foi reanimado com " .. health .. " de vida (vida máxima original), sem penalidade nas estatísticas, por " .. turns .. " turnos.")
                    LUA,
            ],
            "Zoologia" => [
                "Aprendiz em zoologia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; the companion beast sheet (50 creation points) is not modeled.
                    rpg.log(ctx.user.name .. " possui um animal ou besta companheira criada com 50 pontos.")
                    LUA,
                "Formado em zoologia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; +25 points for the companion beast sheet are not modeled.
                    rpg.log(ctx.user.name .. " recebe mais 25 pontos para a ficha de sua besta.")
                    LUA,
                "Mestre em zoologia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; +25 points for the companion beast sheet are not modeled.
                    rpg.log(ctx.user.name .. " recebe mais 25 pontos para a ficha de sua besta.")
                    LUA,
                "Grão-mestre em zoologia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; +25 points for the companion beast sheet are not modeled.
                    rpg.log(ctx.user.name .. " recebe mais 25 pontos para a ficha de sua besta.")
                    LUA,
                "Ancião em zoologia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; +25 points for the companion beast sheet are not modeled.
                    rpg.log(ctx.user.name .. " recebe mais 25 pontos para a ficha de sua besta.")
                    LUA,
            ],
        ];
    }

    /**
     * Maldição, Reforço, Magia negra, Golemancia, Runas, Medicina espiritual, Manipulação espacial and Formalismo.
     *
     * @return array<string, array<string, string>>
     */
    private function curseBodySummonFieldStrategies(): array
    {
        return [
            "Maldição" => [
                "Gandr" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end

                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Gandr"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " esquivou do Gandr.")
                      return
                    end

                    local lost = math.max(0, (target.maxAttributes.max_health or 0) - (target.attributes.health or 0))
                    -- The cast turn counts as the first of the MP turns: roll it now.
                    local tick = rpg.roll(lost, "Gandr (vida perdida)")
                    rpg.damage(1, tick.hits)
                    if mp > 1 then
                      -- TODO: each remaining turn, roll dice equal to the target's CURRENT lost health and deal 1 damage per hit (needs a per-turn effect hook with dynamic dice)
                      rpg.applyEffect(1, "Dilacerado", nil, mp - 1)
                    end
                    rpg.log(target.name .. " recebe " .. tick.hits .. " de dano do Gandr (" .. lost .. " dados).")
                    LUA,
                "Geis" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    if mp ~= 0 then
                      rpg.log("Aviso: Geis não tem custo, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: needs a consent step from both parties and a way to record the contract terms/duration and block actions that break them (GM-enforced for now)
                    rpg.log(ctx.user.name .. " propõe um contrato (Geis) a " .. target.name .. ". Ambas as partes precisam concordar; o mestre aplica as condições.")
                    LUA,
                "Algemar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    if not ctx.targets[1] then rpg.log("Nenhum alvo selecionado.") return end
                    if not ctx.targets[2] then
                      -- TODO: needs two targets; the controller only sends one today
                      rpg.log("Aviso: Algemar precisa de dois alvos; apenas um foi selecionado.")
                    end
                    -- TODO: while linked, each target must take half (rounded up) of the damage the other takes (needs a damage-received hook)
                    for i, t in ipairs(ctx.targets) do
                      rpg.applyEffect(i, "Algemado", nil, mp)
                      rpg.log(t.name .. " está algemado por " .. mp .. " turnos.")
                    end
                    LUA,
                "Amaldiçoar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    -- TODO: needs an input to choose the cursed stat, and the effect must lower that stat by 2 (stats cannot be changed by scripts)
                    rpg.applyEffect(1, "Amaldiçoado", 2, mp)
                    rpg.log(target.name .. " é amaldiçoado: uma estatística escolhida recebe -2 por " .. mp .. " turnos.")
                    LUA,
                "Osteoporose" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    local movement = target.attributes.movement or 0
                    if movement > 1 then
                      rpg.modify(1, "movement", 1 - movement)
                    end
                    -- TODO: movement must stay capped at 1 on every turn refresh while the effect lasts
                    rpg.applyEffect(1, "Fragilizado", 1, mp)
                    rpg.log(target.name .. " tem os pontos de movimento reduzidos para 1 por " .. mp .. " turnos.")
                    LUA,
                "Finn" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end

                    local attack = rpg.contest(
                      (user.skills.ranged or 0) + (user.stats.magic or 0),
                      (target.skills.acrobatics or 0) + (target.stats.agility or 0),
                      "Finn"
                    )
                    if not attack.success then
                      rpg.log(target.name .. " esquivou do Finn.")
                      return
                    end

                    local lost = math.max(0, (target.maxAttributes.max_health or 0) - (target.attributes.health or 0))
                    local roll = rpg.roll(lost + mp, "Finn (vida perdida + MP)")
                    rpg.damage(1, roll.hits)
                    rpg.log(target.name .. " recebe " .. roll.hits .. " de dano verdadeiro do Finn.")
                    LUA,
                "Expor" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 5
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    if mp ~= cost then
                      rpg.log("Aviso: Expor custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: the next attack the target receives must deal double damage and then remove this effect (needs an incoming-attack hook)
                    rpg.applyEffect(1, "Exposto", 2)
                    rpg.log(target.name .. " está exposto: o próximo ataque recebido causa dano dobrado.")
                    LUA,
                "Acorrentar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    local check = rpg.check(target.stats.endurance or 0, mp, "Resistência de " .. target.name)
                    if check.success then
                      rpg.log(target.name .. " resiste às correntes.")
                      return
                    end
                    local turns = -check.diff
                    rpg.applyEffect(1, "Atordoado", nil, turns)
                    rpg.log(target.name .. " é acorrentado e fica atordoado por " .. turns .. " turnos.")
                    LUA,
            ],
            "Reforço" => [
                "Reforçar pernas" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local turns = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    if turns < 1 then
                      rpg.log("MP insuficiente: o reforço dura 0 turnos.")
                      return
                    end
                    local baseMovement = user.maxAttributes.max_movement or user.attributes.movement or 0
                    rpg.modify("user", "movement", baseMovement)
                    -- TODO: movement must stay doubled on every turn refresh while the effect lasts
                    rpg.applyEffect("user", "Pernas reforçadas", 2, turns)
                    rpg.log(user.name .. " reforça as pernas e anda o dobro por " .. turns .. " turnos.")
                    LUA,
                "Reforçar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local bonus = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: should target an object/item (items cannot be targeted), add the bonus dice to every roll involving it, and block reinforcing the same object twice
                    if bonus < 1 then
                      rpg.log("MP insuficiente: o reforço não tem efeito.")
                      return
                    end
                    rpg.applyEffect("user", "Objeto reforçado", bonus, bonus)
                    rpg.log(ctx.user.name .. " reforça um objeto: +" .. bonus .. " dados em rolagens com ele por " .. bonus .. " turnos.")
                    LUA,
                "Reforçar pele" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local reduction = math.floor(mp / 4)
                    local turns = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: incoming damage must be reduced by the effect power (needs a damage-received hook)
                    if turns < 1 then
                      rpg.log("MP insuficiente: o reforço dura 0 turnos.")
                      return
                    end
                    rpg.applyEffect("user", "Pele reforçada", reduction, turns)
                    rpg.log(ctx.user.name .. " reforça a pele: dano recebido -" .. reduction .. " por " .. turns .. " turnos.")
                    LUA,
                "Reforçar braços" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local bonus = math.floor(mp / 4)
                    local turns = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: physical attacks must deal extra true damage equal to the effect power (needs an outgoing-attack hook)
                    if turns < 1 then
                      rpg.log("MP insuficiente: o reforço dura 0 turnos.")
                      return
                    end
                    rpg.applyEffect("user", "Braços reforçados", bonus, turns)
                    rpg.log(ctx.user.name .. " reforça os braços: +" .. bonus .. " de dano verdadeiro em ataques físicos por " .. turns .. " turnos.")
                    LUA,
                "Reforço elemental de fogo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    local burn = math.floor(mp / 3)
                    local burnTurns = math.floor(magic / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to fire alignment (alignment not exposed to scripts)
                    -- TODO: every weapon hit must apply Queimado (power = effect power, duration = floor(Magia / 2)) to the target (needs an on-hit hook)
                    rpg.applyEffect("user", "Arma flamejante", burn, magic)
                    rpg.log(user.name .. " reforça a arma com fogo por " .. magic .. " turnos: ataques queimam em " .. burn .. " por " .. burnTurns .. " turnos.")
                    LUA,
                "Reforço  elemental de água" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to water alignment (alignment not exposed to scripts)
                    -- TODO: weapon attacks must heal the target instead of damaging it, with a total healing cap of MP over the whole effect (needs an on-hit hook and a shared pool)
                    rpg.applyEffect("user", "Arma curativa", mp, magic)
                    rpg.log(user.name .. " reforça a arma com água por " .. magic .. " turnos: ataques curam (máximo total de " .. mp .. ").")
                    LUA,
                "Reforço elemental de ar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    local bonus = math.floor(mp / 3)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to air alignment (alignment not exposed to scripts)
                    -- TODO: grant immunity to ranged physical attacks and add the effect power to dodge rolls (needs incoming-attack/dodge hooks)
                    rpg.applyEffect("user", "Envolto em ar", bonus, magic)
                    rpg.log(user.name .. " reforça o corpo com ar por " .. magic .. " turnos: imune a ataques físicos à distância e +" .. bonus .. " em esquivas.")
                    LUA,
                "Reforço elemental de terra" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    local reduction = math.floor(mp / 3)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to earth alignment (alignment not exposed to scripts)
                    -- TODO: reduce incoming damage by the effect power and deal the mitigated amount as a shockwave to everyone within Resistência range (needs a damage-received hook and area targeting)
                    rpg.applyEffect("user", "Envolto em terra", reduction, magic)
                    rpg.log(user.name .. " reforça o corpo com terra por " .. magic .. " turnos: dano recebido -" .. reduction .. ", convertido em onda de choque.")
                    LUA,
                "Reforço elemental arcano" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    local bonus = math.floor(mp / 3)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to arcane alignment (alignment not exposed to scripts)
                    -- TODO: every weapon hit must deal +effect power damage and apply Predestinado for floor(Magia / 2) turns (needs an on-hit hook)
                    rpg.applyEffect("user", "Arma arcana", bonus, magic)
                    rpg.log(user.name .. " reforça a arma com energia arcana por " .. magic .. " turnos: +" .. bonus .. " de dano e alvos Predestinados.")
                    LUA,
                "Reforço elemental vazio" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local turns = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to void alignment (alignment not exposed to scripts)
                    -- TODO: weapon damage becomes true damage and each hit deals 1 true damage to the user (needs an on-hit hook)
                    if turns < 1 then
                      rpg.log("MP insuficiente: o reforço dura 0 turnos.")
                      return
                    end
                    rpg.applyEffect("user", "Arma do vazio", nil, turns)
                    rpg.log(ctx.user.name .. " reforça a arma com o vazio por " .. turns .. " turnos.")
                    LUA,
                "Reforço elemental elétrico" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    local bounces = math.floor(mp / 3)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to electric alignment (alignment not exposed to scripts)
                    -- TODO: weapon hits must deal +1 damage and ricochet the damage to up to effect-power adjacent targets (needs an on-hit hook and adjacency)
                    rpg.applyEffect("user", "Arma elétrica", bounces, magic)
                    rpg.log(user.name .. " reforça a arma com eletricidade por " .. magic .. " turnos: +1 de dano, ricocheteia em até " .. bounces .. " alvos.")
                    LUA,
                "Reforço elemental gelo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local magic = user.stats.magic or 0
                    local freezeTurns = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: restrict to ice alignment (alignment not exposed to scripts)
                    -- TODO: weapon hits must apply Congelado for floor(MP / 2) turns; hitting a target with Congelado >= 3 converts it into Atordoado lasting 1/3 of the remaining turns (needs an on-hit hook)
                    rpg.applyEffect("user", "Arma congelante", freezeTurns, magic)
                    rpg.log(user.name .. " reforça a arma com gelo por " .. magic .. " turnos: ataques congelam por " .. freezeTurns .. " turnos.")
                    LUA,
                "Reforço excessivo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local bonus = mp * 2
                    local turns = math.floor(mp / 2)
                    -- TODO: the first reinforcement spell of the turn is a free action (action economy not modeled)
                    -- TODO: should target an object/item (items cannot be targeted), add the bonus dice to every roll involving it, and destroy the object permanently when the effect ends
                    if turns < 1 then
                      rpg.log("MP insuficiente: o reforço dura 0 turnos.")
                      return
                    end
                    rpg.applyEffect("user", "Objeto sobrecarregado", bonus, turns)
                    rpg.log(ctx.user.name .. " sobrecarrega um objeto: +" .. bonus .. " dados por " .. turns .. " turnos; depois ele é destruído.")
                    LUA,
                "Reforçar aliados" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; reinforcement scripts only target the user. They should accept an ally (within Magia range, with consent) and charge 2 extra MP that is excluded from the spell's MP math.
                    rpg.log("Reforçar aliados é passivo: permite reforçar aliados e suas armas por +2 MP.")
                    LUA,
            ],
            "Magia negra" => [
                "Estender" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local penalty = math.floor(mp / 2)
                    -- TODO: free action; melee reach must increase by MP squares this turn and melee attack rolls must lose floor(MP / 2) dice (needs range and attack-roll modifiers)
                    rpg.applyEffect("user", "Estendido", mp, 1)
                    rpg.log(ctx.user.name .. " estende os braços em " .. mp .. " quadrados neste turno (-" .. penalty .. " dados no ataque).")
                    LUA,
                "Star finger" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    if not ctx.targets[1] then rpg.log("Nenhum alvo selecionado.") return end
                    -- TODO: line of MP squares; targets must be everyone on the line, in order (needs area targeting)
                    local hitCount = 0
                    for i, t in ipairs(ctx.targets) do
                      local attack = rpg.contest(
                        (user.skills.ranged or 0) + (user.stats.magic or 0),
                        (t.skills.acrobatics or 0) + (t.stats.agility or 0),
                        "Star finger em " .. t.name
                      )
                      if attack.success then
                        local damage = math.max(0, 2 - hitCount)
                        hitCount = hitCount + 1
                        rpg.damage(i, damage)
                        rpg.log(t.name .. " recebe " .. damage .. " de dano do Star finger.")
                      else
                        rpg.log(t.name .. " esquivou do Star finger.")
                      end
                    end
                    LUA,
                "Metamorfia" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 3
                    if mp ~= cost then
                      rpg.log("Aviso: Metamorfia custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: charge 1 MP upkeep per turn while maintained; the chosen animal part and its bonuses are GM-defined
                    rpg.applyEffect("user", "Metamorfoseado")
                    rpg.log(ctx.user.name .. " transforma parte do corpo em uma parte animal (manutenção: 1 MP por turno).")
                    LUA,
                "Pirocóptero" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local radius = math.floor(mp / 2)
                    if not ctx.targets[1] then rpg.log("Nenhum alvo selecionado.") return end
                    -- TODO: female characters need Metamorfia active first (sex/active-spell check not available)
                    -- TODO: circular attack of radius floor(MP / 2); targets must be everyone in range, in the chosen rotation order (needs area targeting)
                    local hitCount = 0
                    for i, t in ipairs(ctx.targets) do
                      local attack = rpg.contest(
                        (user.skills.melee or 0) + (user.stats.strength or 0),
                        (t.skills.acrobatics or 0) + (t.stats.agility or 0),
                        "Pirocóptero em " .. t.name
                      )
                      if attack.success then
                        local damage = math.max(0, mp - hitCount)
                        hitCount = hitCount + 1
                        rpg.damage(i, damage)
                        -- TODO: the effect must lower Inteligência and Agilidade by 1 (stats cannot be changed by scripts)
                        rpg.applyEffect(i, "Desmoralizado", 1, mp)
                        rpg.log(t.name .. " recebe " .. damage .. " de dano e fica desmoralizado por " .. mp .. " turnos.")
                      else
                        rpg.log(t.name .. " esquivou do Pirocóptero.")
                      end
                    end
                    rpg.log("Alcance do giro: " .. radius .. " quadrados.")
                    LUA,
                "Metamorfia completa" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 5
                    if mp ~= cost then
                      rpg.log("Aviso: Metamorfia completa custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: charge 2 MP upkeep per turn while maintained, block spellcasting while transformed; the animal form's stats are GM-defined
                    rpg.applyEffect("user", "Transformado")
                    rpg.log(ctx.user.name .. " transforma-se completamente em um animal (manutenção: 2 MP por turno, sem conjurar magias).")
                    LUA,
            ],
            "Golemancia" => [
                "Aprendiz de golemancia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; enforce max 1 summoned golem and 2 golems in inventory, chosen at the start of the day when summoning golems (summon limits and golem inventory not modeled)
                    rpg.log("Aprendiz de golemancia é passivo: permite invocar e controlar 1 golem e ter até 2 golems no inventário (escolhidos no começo do dia).")
                    LUA,
                "Golem de ataque básico" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 3
                    if mp ~= cost then
                      rpg.log("Aviso: Golem de ataque básico custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem de ataque básico" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem de ataque básico.")
                    LUA,
                "Golem de defesa básico" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 3
                    if mp ~= cost then
                      rpg.log("Aviso: Golem de defesa básico custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem de defesa básico" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem de defesa básico.")
                    LUA,
                "Formado em golemancia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; enforce max 2 summoned golems and 5 in inventory when summoning golems (summon limits and golem inventory not modeled)
                    rpg.log("Formado em golemancia é passivo: até 5 golems no inventário e 2 invocados.")
                    LUA,
                "Golem de ataque avançado" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 5
                    if mp ~= cost then
                      rpg.log("Aviso: Golem de ataque avançado custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem de ataque avançado" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem de ataque avançado.")
                    LUA,
                "Golem de defesa avançado" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 5
                    if mp ~= cost then
                      rpg.log("Aviso: Golem de defesa avançado custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem de defesa avançado" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem de defesa avançado.")
                    LUA,
                "Golem de cura básico" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 6
                    if mp ~= cost then
                      rpg.log("Aviso: Golem de cura básico custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem de cura básico" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem de cura básico.")
                    LUA,
                "Mestre em golemancia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; enforce max 3 summoned golems and 8 in inventory when summoning golems (summon limits and golem inventory not modeled)
                    rpg.log("Mestre em golemancia é passivo: até 8 golems no inventário e 3 invocados.")
                    LUA,
                "Golem de cura avançado" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 7
                    if mp ~= cost then
                      rpg.log("Aviso: Golem de cura avançado custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem de cura avançado" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem de cura avançado.")
                    LUA,
                "Golem espadachim" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 7
                    if mp ~= cost then
                      rpg.log("Aviso: Golem espadachim custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem espadachim" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem needs a melee weapon handed to it (items not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem espadachim.")
                    LUA,
                "Golem arqueiro" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 7
                    if mp ~= cost then
                      rpg.log("Aviso: Golem arqueiro custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem arqueiro" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem needs a ranged weapon handed to it (items not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem arqueiro.")
                    LUA,
                "Golem escudeiro" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 7
                    if mp ~= cost then
                      rpg.log("Aviso: Golem escudeiro custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem escudeiro" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem needs defensive equipment handed to it (items not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem escudeiro.")
                    LUA,
                "Golem bomba" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 4
                    if mp ~= cost then
                      rpg.log("Aviso: Golem bomba custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem bomba" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: self-destruct area explosion must be resolved as a golem action (area damage not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem bomba.")
                    LUA,
                "Grão-mestre em golemancia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; enforce max 4 summoned golems and 11 in inventory when summoning golems (summon limits and golem inventory not modeled)
                    rpg.log("Grão-mestre em golemancia é passivo: até 11 golems no inventário e 4 invocados.")
                    LUA,
                "Golem druida" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 9
                    if mp ~= cost then
                      rpg.log("Aviso: Golem druida custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem druida" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem druida.")
                    LUA,
                "Golem mestre espadachim" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 9
                    if mp ~= cost then
                      rpg.log("Aviso: Golem mestre espadachim custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem mestre espadachim" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem needs a melee weapon handed to it (items not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem mestre espadachim.")
                    LUA,
                "Golem mestre arqueiro" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 9
                    if mp ~= cost then
                      rpg.log("Aviso: Golem mestre arqueiro custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem mestre arqueiro" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem needs a ranged weapon handed to it (items not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem mestre arqueiro.")
                    LUA,
                "Golem mestre escudeiro" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 9
                    if mp ~= cost then
                      rpg.log("Aviso: Golem mestre escudeiro custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem mestre escudeiro" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem needs defensive equipment handed to it (items not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem mestre escudeiro.")
                    LUA,
                "Golem assassino" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 8
                    if mp ~= cost then
                      rpg.log("Aviso: Golem assassino custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem assassino" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem can receive weapons and turn invisible (items/invisibility not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem assassino.")
                    LUA,
                "Ancião em golemancia" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; enforce max 5 summoned golems and 14 in inventory when summoning golems (summon limits and golem inventory not modeled)
                    rpg.log("Ancião em golemancia é passivo: até 14 golems no inventário e 5 invocados.")
                    LUA,
                "Golem elemental de ar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de ar custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de ar" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de ar.")
                    LUA,
                "Golem elemental de fogo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de fogo custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de fogo" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de fogo.")
                    LUA,
                "Golem elemental de terra" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de terra custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de terra" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de terra.")
                    LUA,
                "Golem elemental de água" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de água custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de água" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de água.")
                    LUA,
                "Golem elemental de arcana" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de arcana custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de arcana" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de arcana.")
                    LUA,
                "Golem elemental de vazio" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de vazio custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de vazio" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de vazio.")
                    LUA,
                "Golem elemental de eletricidade" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de eletricidade custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de eletricidade" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de eletricidade.")
                    LUA,
                "Golem elemental de gelo" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 12
                    if mp ~= cost then
                      rpg.log("Aviso: Golem elemental de gelo custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem elemental de gelo" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    rpg.log(ctx.user.name .. " invoca um golem elemental de gelo.")
                    LUA,
                "Golem ancião" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 15
                    if mp ~= cost then
                      rpg.log("Aviso: Golem ancião custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: summon the "Golem ancião" from its sheet in the sheets folder and enforce the summon/inventory limits (summons not modeled)
                    -- TODO: on deactivation without destruction refund floor(cost / 3) MP; on destruction charge floor(cost / 3) MP, converted to true damage if unpaid (needs golem lifecycle hooks)
                    -- TODO: the golem occupies a 2x2 space (positions not modeled)
                    rpg.log(ctx.user.name .. " invoca um golem ancião.")
                    LUA,
            ],
            "Runas" => [
                "Campo de isolamento espacial" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: needs an input for the number of runes placed (assumes the minimum of 3)
                    local runes = 3
                    local turns = mp + runes
                    -- TODO: rune placement and the field area are not modeled; nothing may enter or leave the field (GM-enforced)
                    rpg.log(ctx.user.name .. " ativa um campo de isolamento espacial com " .. runes .. " runas por " .. turns .. " turnos.")
                    LUA,
                "Campo de transformação espacial" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: needs an input for the number of runes placed (assumes the minimum of 3)
                    local runes = 3
                    local turns = mp + runes
                    -- TODO: rune placement and the field area are not modeled; terrain inside becomes flat and unobstructed (GM-enforced)
                    rpg.log(ctx.user.name .. " ativa um campo de transformação espacial com " .. runes .. " runas por " .. turns .. " turnos.")
                    LUA,
                "Campo de dreno" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: needs an input for the number of runes placed (assumes the minimum of 3)
                    local runes = 3
                    local turns = mp + runes
                    local penalty = math.floor(runes / 2)
                    -- TODO: targets must be every enemy inside the field, entering/leaving it updates the effect (needs area targeting)
                    -- TODO: the effect must lower ALL stats of the target by its power (stats cannot be changed by scripts)
                    for i, t in ipairs(ctx.targets) do
                      rpg.applyEffect(i, "Drenado", penalty, turns)
                      rpg.log(t.name .. " recebe -" .. penalty .. " em todas as estatísticas.")
                    end
                    rpg.log(ctx.user.name .. " ativa um campo de dreno com " .. runes .. " runas por " .. turns .. " turnos.")
                    LUA,
                "Campo de dissolução" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: needs an input for the number of runes placed (assumes the minimum of 3)
                    local runes = 3
                    local turns = mp + runes
                    -- TODO: targets must be every enemy inside the field (needs area targeting); damage must repeat every turn while the field lasts (needs a per-turn field hook)
                    for i, t in ipairs(ctx.targets) do
                      rpg.damage(i, runes)
                      rpg.log(t.name .. " recebe " .. runes .. " de dano do campo de dissolução.")
                    end
                    rpg.log(ctx.user.name .. " ativa um campo de dissolução com " .. runes .. " runas por " .. turns .. " turnos.")
                    LUA,
                "Campo de cura" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    -- TODO: needs an input for the number of runes placed (assumes the minimum of 3)
                    local runes = 3
                    local turns = mp + runes
                    -- TODO: every 3 turns (starting 3 turns after casting) heal every ally inside the field by the rune count (needs area targeting and a per-turn field hook)
                    rpg.log(ctx.user.name .. " ativa um campo de cura com " .. runes .. " runas por " .. turns .. " turnos: cura " .. runes .. " a cada três turnos.")
                    LUA,
                "Campo de roubo de vida" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    -- TODO: needs an input for the number of runes placed (assumes the minimum of 3)
                    local runes = 3
                    local turns = mp + runes
                    -- TODO: targets must be every enemy inside the field (needs area targeting); the drain must repeat every turn while the field lasts (needs a per-turn field hook)
                    local drained = 0
                    for i, t in ipairs(ctx.targets) do
                      local damage = math.min(runes, math.max(0, t.attributes.health or 0))
                      rpg.damage(i, runes)
                      drained = drained + damage
                      rpg.log(t.name .. " recebe " .. runes .. " de dano do campo de roubo de vida.")
                    end
                    -- TODO: the drained health must be split equally among all allies inside the field; only the caster is healed for now
                    local missing = math.max(0, (user.maxAttributes.max_health or 0) - (user.attributes.health or 0))
                    local healed = math.min(drained, missing)
                    rpg.heal("user", healed)
                    rpg.log(user.name .. " ativa um campo de roubo de vida com " .. runes .. " runas por " .. turns .. " turnos e recupera " .. healed .. " de vida.")
                    LUA,
                "Ath nGabla" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 5
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    if mp ~= cost then
                      rpg.log("Aviso: Ath nGabla custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: both duelists are intangible only to everyone else, cannot leave a 10-square radius, and the effect ends only when one dies (needs selective intangibility, positions and a death hook)
                    rpg.applyEffect("user", "Em duelo")
                    rpg.applyEffect(1, "Em duelo")
                    rpg.log(ctx.user.name .. " desafia " .. target.name .. " para um duelo até a morte (Ath nGabla).")
                    LUA,
                "Campo múltiplo" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; field scripts should accept a field count and charge MP multiplied by it while still using the chosen MP for the field's numbers
                    rpg.log("Campo múltiplo é passivo: permite criar vários campos com as mesmas runas, multiplicando o custo de MP.")
                    LUA,
            ],
            "Medicina espiritual" => [
                "Curar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local sheet = target or ctx.user
                    -- TODO: target must be within melee range (positions not modeled)
                    local roll = rpg.roll(mp, "Curar")
                    local missing = math.max(0, (sheet.maxAttributes.max_health or 0) - (sheet.attributes.health or 0))
                    local healed = math.min(roll.hits, missing)
                    rpg.heal(who, healed)
                    rpg.log(sheet.name .. " é curado em " .. healed .. " de vida.")
                    LUA,
                "Reabastecimento" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local sheet = target or user
                    local dice = math.floor((user.stats.magic or 0) / 2)
                    -- TODO: target must be within melee range (positions not modeled)
                    if mp < 1 then
                      rpg.log("MP insuficiente: o reabastecimento dura 0 turnos.")
                      return
                    end
                    -- The cast turn counts as the first of the MP turns: roll it now.
                    local roll = rpg.roll(dice, "Reabastecimento")
                    local missing = math.max(0, (sheet.maxAttributes.max_health or 0) - (sheet.attributes.health or 0))
                    local healed = math.min(roll.hits, missing)
                    rpg.heal(who, healed)
                    if mp > 1 then
                      -- TODO: each remaining turn, roll floor(Magia / 2) dice and heal 1 per hit (needs a per-turn effect hook)
                      rpg.applyEffect(who, "Revigorado", dice, mp - 1)
                    end
                    rpg.log(sheet.name .. " é curado em " .. healed .. " de vida (reabastecimento por " .. mp .. " turnos).")
                    LUA,
                "Meditar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local size = user.stats.magic or 0
                    local upkeep = math.floor(mp / 4)
                    -- TODO: charge floor(MP / 4) upkeep per turn while maintained and end the zone when Concentrado is lost
                    -- TODO: from the next turn on, heal everyone within Magia range by the hits of an MP-dice roll each turn (needs area targeting and a per-turn hook)
                    rpg.applyEffect("user", "Concentrado")
                    rpg.log(user.name .. " medita, criando uma zona de cura de tamanho " .. size .. " (manutenção: " .. upkeep .. " MP por turno).")
                    LUA,
                "Cura avançada" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local sheet = target or ctx.user
                    -- TODO: target must be within melee range (positions not modeled)
                    local roll = rpg.roll(2 * mp, "Cura avançada")
                    local missing = math.max(0, (sheet.maxAttributes.max_health or 0) - (sheet.attributes.health or 0))
                    local healed = math.min(roll.hits, missing)
                    rpg.heal(who, healed)
                    rpg.log(sheet.name .. " é curado em " .. healed .. " de vida.")
                    LUA,
                "Tiro de cura" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local target = ctx.targets[1]
                    local who = target and 1 or "user"
                    local sheet = target or user
                    local range = 2 * (user.stats.magic or 0)
                    -- TODO: target must be within 2 * Magia squares (positions not modeled)
                    local roll = rpg.roll(mp, "Tiro de cura")
                    local missing = math.max(0, (sheet.maxAttributes.max_health or 0) - (sheet.attributes.health or 0))
                    local healed = math.min(roll.hits, missing)
                    rpg.heal(who, healed)
                    rpg.log(sheet.name .. " é curado à distância (alcance " .. range .. ") em " .. healed .. " de vida.")
                    LUA,
            ],
            "Manipulação espacial" => [
                "Piscar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local user = ctx.user
                    local movement = user.attributes.movement or 0
                    local magic = user.stats.magic or 0
                    local extraSquares = math.max(0, mp - 1)
                    if mp < 1 then
                      rpg.log("Aviso: Piscar custa pelo menos 1 MP.")
                    end
                    if extraSquares > magic then
                      rpg.log("Aviso: o alcance máximo além do movimento é Magia (" .. magic .. " quadrados).")
                    end
                    -- TODO: needs the teleport distance as input to spend movement points first and 1 MP per square beyond them; positions are not modeled
                    rpg.log(user.name .. " pisca: alcance de até " .. (movement + magic) .. " quadrados (" .. extraSquares .. " quadrados extras pagos com MP).")
                    LUA,
                "Telecinese" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local magic = ctx.user.stats.magic or 0
                    -- TODO: the MP cost depends on the object's size and weight (GM-defined); objects and positions are not modeled
                    rpg.log(ctx.user.name .. " move um objeto dentro de um alcance de " .. magic .. " quadrados usando " .. mp .. " MP.")
                    LUA,
                "Transportar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 3
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    if mp ~= cost then
                      rpg.log("Aviso: Transportar custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    -- TODO: requires the ally's consent and moves them to a point within Magia range (needs a consent step and positions)
                    rpg.log(ctx.user.name .. " transporta " .. target.name .. " dentro de um alcance de " .. (ctx.user.stats.magic or 0) .. " quadrados.")
                    LUA,
                "Sequestrar" => <<<'LUA'
                    local ctx = ...
                    local mp = ctx.input.cost or 0
                    local cost = 4
                    local user = ctx.user
                    local target = ctx.targets[1]
                    if not target then rpg.log("Nenhum alvo selecionado.") return end
                    if mp ~= cost then
                      rpg.log("Aviso: Sequestrar custa " .. cost .. " MP, mas foram usados " .. mp .. " MP.")
                    end
                    local contest = rpg.contest(
                      (user.stats.magic or 0) + cost,
                      (target.skills.tenacity or 0) + (target.stats.endurance or 0),
                      "Sequestrar"
                    )
                    if not contest.success then
                      rpg.log(target.name .. " resiste ao Sequestrar.")
                      return
                    end
                    -- TODO: move the target to a point within Magia range (positions not modeled)
                    rpg.log(user.name .. " teletransporta " .. target.name .. " dentro de um alcance de " .. (user.stats.magic or 0) .. " quadrados.")
                    LUA,
            ],
            "Formalismo" => [
                "Aprendiz de formalismo" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive modifier declared with another spell; needs channel-turn input, Concentrado while channeling (losing all mana if interrupted), a per-location mana pool, and reducing the spell's MP cost by 1 per channeled turn
                    rpg.log("Aprendiz de formalismo: permite usar 1 de mana do ambiente por turno de canalização (requer Concentrado).")
                    LUA,
                "Mestre em formalismo" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; raises formalism to 2 mana per channeled turn and lets it cover 1/2 of the MP cost of potion crafting (needs the formalism channel and crafting hooks)
                    rpg.log("Mestre em formalismo: 2 de mana do ambiente por turno e cobre ½ do custo de MP na criação de poções.")
                    LUA,
                "Ancião em formalismo" => <<<'LUA'
                    local ctx = ...
                    -- TODO: passive; raises formalism to 3 mana per channeled turn and lets it fill 1/2 of a gem's maximum capacity (needs the formalism channel and gem hooks)
                    rpg.log("Ancião em formalismo: 3 de mana do ambiente por turno e preenche ½ da capacidade máxima de gemas.")
                    LUA,
            ],
        ];
    }
}
