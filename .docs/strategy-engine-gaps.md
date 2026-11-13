# Strategy engine: what's missing to run every spell

Every spell in `database/seeders/data/Magic.txt` (173 spells) has a Lua strategy seeded by
`Database\Seeders\Strategy\MagicStrategySeeder`. Where a rule can't be expressed with the current engine, the
script implements what it can and marks the rest with `-- TODO:`. There are 265 of these, and you can list them with
`grep -n "TODO" database/seeders/Strategy/MagicStrategySeeder.php`.

This document groups those gaps by the engine capability that's missing, so they can be planned as features.
Each section lists the spells that depend on it. Spell names are as written in Magic.txt.

## What the engine has today

- **Context** (`App\Entities\StrategyContext`): `ctx.user` and `ctx.targets` are read-only sheet snapshots
  (`StrategySheet`). `ctx.input` (`StrategyInput`) only carries `cost`.
- **Library** (`App\Services\Strategy\StrategyLibrary`): `roll`, `check`, `contest`, `damage`, `heal`, `modify`
  (attributes only), `applyEffect`, `removeEffect` and `log`.
- **Execution:** a strategy runs once, when the action is used. Changes are applied only if the whole script
  succeeds. Effect strategies are stored but never executed.
- **Controller** (`RollController::rollSpell`): deducts the typed `cost` from mana and rolls recoil before the
  script runs. It resolves at most one target.

## 0. Blocker: the `effects` table is empty

`rpg.applyEffect` throws on an unknown effect name, and a thrown error discards every change the script made.
Until these rows exist, most spells fail at runtime. These are the 54 names the seeded scripts apply:

- **Conditions:** Queimado, Cauterizado, Molhado, Derrubado, Atordoado, Congelado, Predestinado, Voo, Banido,
  Concentrado, Intangível, Envenenado, Enfraquecido
- **Elemental:** Energizado, Flutuante, Invisível, Fortificado, Protegido, Enterrado
- **Numerology and astromancy:** Previsto, Equacionado, Enviesado, Destinado, Prognosticado, Alinhado
- **Botany and necromancy:** Melhorado, Fortalecido, Regenerando, Reanimado
- **Curses:** Dilacerado, Algemado, Amaldiçoado, Fragilizado, Exposto
- **Reinforcements:** Pernas reforçadas, Objeto reforçado, Pele reforçada, Braços reforçados, Objeto sobrecarregado,
  Arma flamejante, Arma curativa, Envolto em ar, Envolto em terra, Arma arcana, Arma do vazio, Arma elétrica,
  Arma congelante
- **Body magic, runes and healing:** Estendido, Desmoralizado, Metamorfoseado, Transformado, Drenado, Em duelo, Revigorado

`Exausto` is referenced but never applied, because it needs an expiry hook (see section 1).

Needed: an `EffectSeeder` (or effects added to `MagicStrategySeeder`) with names and descriptions. Optionally, a
Lua strategy per effect once effects are executed (see section 1).

## 1. Effect lifecycle: ticking, expiry and expiry hooks

**Missing:** durations never decrease. Effects never act on their own each turn and never expire. Nothing runs
when an effect ends.

**Needed:**
- A turn or round loop that decrements `remaining_duration` and runs each effect's strategy every turn.
- A hook for when an effect ends.
- The effect's `power` and `duration` passed to its strategy.

**Spells:**
- **Damage or healing every turn:** Toque flamejante, Dardo de chamas, Esfera de fogo, Explosão and Ragnarok
  (Queimado); Gandr (Dilacerado); Poção de veneno (Envenenado); Poção de regeneração (Regenerando);
  Reabastecimento (Revigorado); Supernova; Tumba at 9+ hits; Nevasca; Bolha in damage mode.
- **Something happens when an effect ends:**
  - Energia applies Exausto.
  - Caminho do vazio deals damage and pushes when Banido ends.
  - Reforço excessivo destroys the object.
  - Necromancia: the revived target dies again.
  - Horóscopo resolves 3 turns after the cast.
  - Destino: the bonus lasts MP turns.
- **Rules that need durations to be readable or changeable:**
  - Remover efeito shortens durations by 1 per hit; it needs something like `rpg.extendEffect(alvo, nome, delta)`.
  - Nevasca extends Congelado up to ½ MP.
  - Freeze-to-stun conversion in Toque congelante, Dardo de gelo and Reforço elemental gelo.

## 2. Multiple targets

**Missing:** `StrategyEngine::run()` and `ctx.targets` already accept n targets. But
`RollController::resolveTargets()` reads a single `target` request field, and the UI only lets you pick one.

**Needed:** a multi-target selection in the request and UI.

**Spells:** these scripts already loop over `ctx.targets` and work as soon as more than one target is passed.
- **Fogo:** Explosão, Ragnarok
- **Água:** Vida líquida, Onda
- **Ar:** Singularidade
- **Vazio:** Supernova
- **Eletricidade:** Toque chocante, Orbe elétrico, Avanço, Descarga, Trovoada, Tempestade
- **Gelo:** Nevasca, Patinação
- **Mineralogia:** Estourar, Atirar
- **Astromancia:** Tiro de sorte, Chuva de meteoros, Horóscopo (caster and target)
- **Maldição:** Algemar (needs exactly two targets)
- **Magia negra:** Star finger, Pirocóptero
- **Runas:** every field
- **Medicina espiritual:** Meditar

## 3. Event triggers (hooks)

**Missing:** a strategy can only act when it is cast.

**Needed:** effect strategies that subscribe to events and can change the event's numbers:
- the bearer's next or every attack (on hit)
- the bearer's next or every roll (add or remove dice, count hits differently, change hits)
- incoming attack or damage (reduce, redirect, double, roll before the dodge)
- a character's death
- the caster being attacked while channeling

**Spells:**
- **Next attack or on hit:**
  - Toques: Toque flamejante, molhado, de vento, arcano, vazio, chocante and congelante, plus Duro como pedra.
    Today these resolve immediately as a melee attack.
  - Poção de enfraquecimento and Poção de veneno applied to a weapon.
  - Weapon effects: Vento invisível (−2 to dodge against the weapon) and Reforçar braços.
  - All 8 elemental reinforcements (Reforço elemental de fogo, água, ar, terra, arcano, vazio, elétrico and gelo).
- **Rolls:**
  - Previsão (±5 dice on the next relevant roll).
  - Alinhamento (doubles the hits if they equal the declared number).
  - Equacionar (copy hits from an earlier roll and paste them into a later one).
  - Viés (3–6 count as hits).
  - Reforçar and Reforço excessivo (extra dice on rolls involving the object).
  - Exausto (−2 dice on every roll).
- **Incoming attack or damage:**
  - Expor (double damage).
  - Algemar (half the damage is shared).
  - Barreira (defense roll before the dodge, −1 damage).
  - Reforçar pele (damage reduction).
  - Reforço elemental de terra (reduction, plus a shockwave).
  - Reforço elemental de ar (immune to ranged physical attacks, dodge bonus).
  - Reforço elemental de água (attacks heal instead of damage, up to an MP cap).
- **Death:** Destino (bonus to whoever took part in the kill; also needs to track who took part).
- **Channel interrupted when attacked:** Evocar elemental, Evocar doppelganger, Formalismo.

## 4. Stat and skill modifiers

**Missing:** `rpg.modify` only changes `attributes`. Stats and skills can't change, even temporarily, and an
attribute changed today is overwritten when it refreshes at the start of the turn.

**Needed:** temporary modifiers to stats, skills and maximum values, owned by an effect and removed when it
expires. Also, a way for an effect to cap or multiply an attribute that refreshes (movement).

**Spells:**
- **Stat changes:**
  - Fortificar (+Resistência).
  - Cauterizado (−Resistência), applied by the fire spells.
  - Amaldiçoar (−2 to a chosen stat).
  - Campo de dreno (−runes/2 to all stats).
  - Pirocóptero (−1 Inteligência and Agilidade).
  - Poção de melhoramento, Poção de fortalecimento and Poção de enfraquecimento.
  - Destino (a random stat from a d8).
  - Necromancia (−1 to every stat of the revived).
  - Evocar doppelganger (the copy has −1 to every stat).
- **Movement that must persist:** Osteoporose (capped at 1), Reforçar pernas (doubled), Avanço (Ar) (+MP),
  Patinação (+Magia).
- **Restrictions on what a character can do:**
  - Tumba: can't move, attack, dodge with Acrobacia or speak, depending on burial depth.
  - Metamorfia completa: can't cast spells while transformed.

## 5. Positions, range and areas

**Missing:** the engine has no grid, positions or distances.

**Needed:** character positions on a map, plus functions for distance, characters within a radius, cone and line
shapes, nearest character, adjacency, line of sight, moving a character, and terrain types.

**Spells:**
- **Range checks:** almost every targeted spell, for example Dardo de chamas (MP+1), Qué ota (10), Remover efeito
  (6), Tiro de cura (2×Magia), Reforçar aliados (Magia).
- **Areas and radius:** Explosão (radius 5), Ragnarok, Singularidade, Descarga, Trovoada, Tempestade, Nevasca,
  Patinação, Chuva de meteoros (9), Meditar, every rune field, Ath nGabla (10).
- **Cone and line:** Estourar (cone, blocked by obstacles), Atirar (piercing line), Star finger (line),
  Pirocóptero (circle).
- **Nearest character and adjacency:** Toque molhado (heals the nearest within 6), Vida líquida (bounces 1
  square), Tiro de sorte (bounces to the nearest), and the electric ricochets (Toque chocante, Orbe elétrico,
  Avanço, Descarga, Trovoada, Tempestade, Reforço elemental elétrico).
- **Push, pull and forced movement:** Toque de vento, Ventania (collisions), Singularidade (pull), Caminho do vazio,
  Terraformar (pushes to the side), Onda.
- **Teleport and moving characters or objects:** Avanço (Eletricidade), Trovoada, Piscar, Transportar, Sequestrar,
  Telecinese.
- **Distance limits from the caster:** Benção da deusa do lago (15), Voo (Magia×2).
- **Terrain:** Onda and Benção da deusa do lago (water), Piso congelado (ice or snow), Terraformar (walls),
  Campo de transformação espacial (flattened terrain), Campo de isolamento espacial (barrier).

## 6. Turns and action economy

**Missing:** no turn order, no action budget, no costs charged each turn, and nothing tracks concentration.

**Needed:**
- A turn loop that charges upkeep and dispels when mana runs out.
- A Concentrado state that breaks.
- Multi-turn channeling that resolves later.
- Action types: action, free action, out of turn.
- Extra actions.
- Limits per turn, round and day.

**Spells:**
- **Upkeep each turn:** Ragnarok (½ MP), Benção da deusa do lago (1 per character), Voo (2), Fortificar (½ MP),
  Reescrever (4), Viés (5), Criar (¼ of the cost), Metamorfia (1), Metamorfia completa (2), Meditar (¼ MP),
  Patinação (½ MP), and every evocation (1, 1, 1, 2, 5, or half of Magia).
- **Concentration that can break:** Ragnarok, Singularidade, Tumba, Poder ilimitado, Tempestade, Patinação,
  Meditar, Formalismo.
- **Channel turns before the effect:**
  - 1 turn: Singularidade, Poder ilimitado, Tempestade, Patinação, Ragnarok (the area forms the next turn),
    Evocar pombo, Evocar rato, Evocar lobo, Evocar espírito.
  - 2 turns: Evocar elemental, Evocar doppelganger.
  - Formalismo channels for a variable number of turns.
- **Free or out-of-turn actions:**
  - Usable as free actions: Benção da deusa do lago, Avanço (Ar), Voo, Barreira, Imprudência, Estender.
  - Usable out of turn: Equacionar, Alinhamento.
  - Only the first use each turn is free: Copiar, and every Reforço spell.
- **Extra actions:** Energia (2 per turn), Poder ilimitado (+½ MP).
- **Limits:**
  - Avanço (Ar) and Voo: once per turn. Barreira: once per round.
  - Poder ilimitado: ⅓ Magia times per day.
  - Botânica: 1 to 5 potions per day.
  - Golemancia: caps on golems in the inventory and summoned at once.
  - Reforço: an object can't be reinforced twice at the same time.

## 7. Cost and recoil control

**Missing:** the controller deducts whatever cost the player typed and rolls recoil before the script runs.
Scripts can't see the recoil, can't change how it's calculated, and can only log a warning when a fixed-cost
spell was cast with a different cost.

**Needed:**
- Cost metadata per spell (fixed, minimum or formula), validated on the server.
- A way for the strategy to set a recoil multiplier, skip recoil or change the cost. For example a pre-cast step,
  or recoil computed by the strategy through `rpg.recoil()`.

**Spells:**
- **Fixed or computed cost:**
  - **Numerologia and Astromancia:** Previsão (5), Reescrever (10), Viés (10), Equacionar (= hits copied),
    Alinhamento (= declared number), Roleta brasileira (10).
  - **Criação:** Copiar (3), Imaginar (4), Armazenar (5).
  - **Maldição and Runas:** Expor (5), Ath nGabla (5), Geis (free).
  - **Manipulação espacial:** Piscar (1 + 1 per extra square), Transportar (3), Sequestrar (4).
  - **Other elemental spells:** Benção da deusa do lago (1), Voo (2), Último suspiro (½ of the damage).
  - **Summons:** Evocar doppelganger (= Magia), every evocation and golem.
- **Recoil rules:**
  - Doubled for Toque vazio, Dardo vazio and Caminho do vazio.
  - Halved, with no MP cost, for electric spells inside Tempestade.
  - Teoria rolls recoil first and only takes effect if the caster survives.
  - Armazenamento applies recoil when the spell is stored.
  - Imprudência has no recoil and no cost.
  - Transferência and Formalismo reduce MP while recoil still applies.

## 8. Cast inputs

**Missing:** `StrategyInput` only has `cost`. The seeded scripts already read `ctx.input.mode` (Bolha) and
`ctx.input.gems` (Liberação Múltipla), which are always `nil` today.

**Needed:** an input schema per spell, rendered as extra fields on the cast form and added as typed properties on
`StrategyInput`.

**Spells:**
- **Mode or choice:**
  - Bolha (damage or heal).
  - Piso congelado (ice or snow).
  - Ventania (controlled push).
  - Previsão (add or remove dice).
  - Remover efeito (which effects).
  - Roleta brasileira (coin or die).
- **Number:**
  - Alinhamento (the declared number).
  - Horóscopo (both predictions).
  - Imprudência (HP to convert; today it reuses `cost`).
  - Piscar (distance).
  - Liberação Múltipla (number of gems).
  - Rune fields (number of runes; the scripts assume 3).
  - Campo múltiplo (number of fields).
- **Stat:** Amaldiçoar, Poção de melhoramento, Poção de fortalecimento, Poção de enfraquecimento.
- **Object or summon:** Reforçar, Reforço excessivo, Telecinese, Evocar espírito (which named spirit), golem type.

## 9. Persistent entities: summons and spell objects

**Missing:** nothing a spell creates can exist after the cast.

**Needed:**
- Summons as sheets owned by the caster, with upkeep, dispelling, deactivation and destruction rules.
- Spell objects with position, duration and their own per-turn strategy.

**Spells:**
- **Summons:**
  - Evocação espiritual: Evocar pombo na coleira pássaro, Evocar rato, Evocar lobo, Evocar espírito,
    Evocar elemental, Evocar doppelganger (copies HP and negative effects).
  - Golemancia: every golem. Destroying a golem costs ⅓ of its MP, or true damage if the caster can't pay.
    Deactivating one refunds ⅓.
  - Necromancia: the revived enemy, under the caster's control.
  - Zoologia: the beast companion (50 + 25 per level in build points).
  - Esfera de fogo: a controllable sphere whose strength drops each turn.
- **Spell objects:**
  - Esfera arcana, which Manipulação arcana, Sobrecarga arcana and Cofre arcano act on.
  - Terraformar (walls with 3 HP per square).
  - Areas: Piso congelado, Patinação, Tempestade, Ragnarok, Nevasca (follows the caster), Meditar.
  - Singularidade.
  - Every rune field.
- **Agreements and bindings:** Geis (an unbreakable contract), Ath nGabla (a duel until death).

## 10. Items and objects

**Missing:** strategies can't read or create items, and effects can't be attached to an item.

**Needed:**
- Item access in the context.
- `rpg` functions to create, consume, store and destroy items.
- Item-bound effects (`effect_item` already exists in the schema).

**Spells:**
- **Gems:** every Mineralogia spell (Transferência stores MP; Liberação, Estourar, Atirar and Liberação Múltipla
  spend it; Armazenamento stores a spell). Ancião em formalismo fills gems.
- **Potions:** every Botânica potion. Today each one is created and used in the same cast. Mestre em formalismo
  covers half the potion cost.
- **Created objects:** Copiar (−1 die, breaks after one use), Imaginar, Armazenar (the caster remembers the
  object), Criar (a perfect copy with upkeep).
- **Reinforced objects and weapons:** Reforçar, Reforço excessivo (destroyed at the end), Vento invisível, the
  weapon reinforcements, potions applied to weapons, and golem weapons and gear.

## 11. Sheet data missing from the context

**Missing:** `StrategySheet` lacks data some rules depend on.

**Needed:** add these as `StrategySheet` properties.

**Spells:**
- **Alignment** (`SheetEntity::$alignment` exists): the 8 "only for X alignment" elemental reinforcements,
  Evocar elemental (fire only).
- **Whether a character is dead:** Necromancia uses `health <= 0` as a stand-in.
- **The character's own spells, schools and levels:** Necromancia and Golemancia rank rules, Reforçar aliados,
  Campo múltiplo, Formalismo.
- **Body or form state:** Pirocóptero's requirement.

## 12. Consent and opposition

**Missing:** there's no way to ask another player for something mid-cast.

**Needed:** a pending prompt to a target player (accept or refuse) before the strategy resolves.

**Spells:**
- **The target must agree:** Transportar, Reforçar aliados, Geis (both parties).
- **The target may refuse the copied hits:** Equacionar.

## 13. Lower priority

- **True damage:** `rpg.damage` makes no difference between normal and true damage ("dano verdadeiro"). It only
  matters once some damage reduction exists (Reforçar pele, Reforço elemental de terra, armor). It's used by every
  Vazio spell, Finn, Reforçar braços, Reforço elemental vazio, Singularidade, Nevasca, Poção de veneno and golem
  destruction.
- **Healing cap:** `rpg.heal` doesn't cap at `max_health`, so scripts cap it themselves. Evocar doppelganger
  mentions HP above the maximum, so decide whether overhealing is allowed before capping it in the library.
- **Random stat:** Destino rolls a d8 mapped to stats in sheet order. This needs a stable stat order exposed to Lua.

## Left to the GM (not planned for automation)

- Reescrever (temporary rule changes).
- Teoria (rewrite a fact; the GM sets the cost).
- Geis (the contract's terms).
- Telecinese (the GM sets the cost by the object's size).
- Criar (variable cost).
- Metamorfia and Metamorfia completa (animal traits).

These scripts log what happened and leave the ruling to the GM.

## Suggested order

1. Seed the effects (section 0). Most scripts fail without them.
2. Multiple targets (section 2). The engine already supports them; only the controller and UI need to change.
3. Effect lifecycle (section 1) and the turn loop (section 6). Most per-turn rules depend on them.
4. Cast inputs (section 8) and cost/recoil control (section 7). These are small, contained changes.
5. Stat modifiers (section 4) and event triggers (section 3).
6. Positions (section 5), persistent entities (section 9) and items (section 10). These are the largest pieces.
