# TYPE-MOON RPG

**English** | [Português (Brasil)](README.pt-BR.md)

A web companion for a homebrew tabletop RPG. Players build and manage their character sheets in the browser. The whole table rolls dice together and sees each result as it happens. The game master manages the game's content from a separate admin panel.

> **Game rules and system documentation:** [Google Drive folder](https://drive.google.com/drive/folders/1TB1rPKTv7qRdFhKAb8BwywwTOcX46e3M?usp=sharing)

The project is in early development, so features and screens may still change.

## What it does

### Character sheets

Each player has one character sheet that collects everything about their character on a single page:

- **Character info:** name, portrait, alignment and organization.
- **Attributes:** health, initiative, movement and the resource pools each archetype uses (mana, energy, blood points, circuits, scripture points). Their maximums are worked out from the character's stats.
- **Stats and skills:** the character's core numbers, which feed into every roll.
- **Items:** the character's inventory.
- **Advantages:** traits and perks bought for the character.
- **Mystic eyes:** special eye powers that can be used on other characters at the table.

The sheet changes with the character's build. Investing in certain stats unlocks the archetype that goes with it, and its section appears on the sheet:

| Archetype | Unlocked by | Adds |
| --- | --- | --- |
| Mage | Magic | Schools of magic and their spells |
| Cleric | Faith | Miracles and scripture abilities |
| Vampire | Lineage | Sonatas and sonata abilities |
| Magitech | Tech | Systems and subsystems |

### Building a character with creation points

Characters are built from a pool of creation points. Raising a stat or skill, joining a school, or buying an advantage, miracle, mystic eye or ability all cost points. The sheet shows the remaining balance live.

New abilities are chosen from in-game shops. Each shop lists only the options that fit the character, such as the sonatas, schools or subsystems they can actually take.

The sheet also enforces the system's limits. It won't save if the character has overspent their points or has more schools or sonatas than their stats allow.

### Dice rolling

Rolls use pools of six-sided dice. Every 5 or 6 counts as a hit. Players can roll:

- **Skills:** the skill and its linked stat, plus any situational modifier.
- **Spells:** a casting roll, plus a recoil check that can hurt the caster if the spell costs more than they can handle. Mana and health are updated automatically.
- **Items and mystic eyes:** mystic eyes can target another character.
- **Generic rolls:** any number of dice, for anything else.

### Playing together in real time

Every roll is broadcast to everyone at the table immediately. A shared roll history shows the 20 most recent rolls: who rolled, their portrait, what they rolled for, and the result. The app also keeps track of which characters are currently online, so other players can target them.

### Game master admin panel

A separate admin area lets the game master manage all of the game's content: users, character sheets, schools and spells, sonatas and their abilities, miracles, scriptures, mystic eyes, effects, advantages, items, and magitech systems and subsystems. Anything added there becomes available to players in the sheet's shops.

### Personalization

Players can pick from a range of visual themes, including light, dark, retro, cyberpunk, pastel and forest.

## License

Released under the [MIT License](LICENSE).
