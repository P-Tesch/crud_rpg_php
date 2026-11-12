# TYPE-MOON RPG

[English](README.md) | **Português (Brasil)**

Um companheiro web para um RPG de mesa autoral. Os jogadores criam e gerenciam suas fichas de personagem no navegador. A mesa inteira rola dados junta e vê cada resultado na hora. O mestre gerencia o conteúdo do jogo em um painel administrativo separado.

> **Regras do jogo e documentação do sistema:** [pasta no Google Drive](https://drive.google.com/drive/folders/1TB1rPKTv7qRdFhKAb8BwywwTOcX46e3M?usp=sharing)

O projeto está no início do desenvolvimento, então funcionalidades e telas ainda podem mudar.

## O que ele faz

### Fichas de personagem

Cada jogador tem uma ficha de personagem que reúne tudo sobre o personagem em uma única página:

- **Informações:** nome, retrato, alinhamento e organização.
- **Atributos:** vida, iniciativa, movimento e as reservas de recursos que cada arquétipo usa (mana, energia, pontos de sangue, circuitos, pontos de escritura). Os valores máximos são calculados a partir das estatísticas do personagem.
- **Estatísticas e habilidades:** os números centrais do personagem, que entram em todas as rolagens.
- **Inventário:** os itens do personagem.
- **Vantagens:** características e benefícios comprados para o personagem.
- **Olhos místicos:** poderes oculares especiais que podem ser usados em outros personagens da mesa.

A ficha muda conforme a construção do personagem. Investir em certas estatísticas desbloqueia o arquétipo correspondente, e a seção dele aparece na ficha:

| Arquétipo | Desbloqueado por | Adiciona |
| --- | --- | --- |
| Mago | Magia | Escolas de magia e suas magias |
| Clérigo | Fé | Milagres e habilidades de escritura |
| Vampiro | Linhagem | Sonatas e habilidades de sonata |
| Magiteck | Tecnologia | Sistemas e subsistemas |

### Criando um personagem com pontos de criação

Os personagens são construídos a partir de uma reserva de pontos de criação. Aumentar uma estatística ou habilidade, entrar em uma escola ou comprar uma vantagem, milagre, olho místico ou habilidade custa pontos. A ficha mostra o saldo restante em tempo real.

Novas habilidades são escolhidas em lojas dentro do jogo. Cada loja lista apenas as opções que fazem sentido para o personagem, como as sonatas, escolas ou subsistemas que ele pode de fato adquirir.

A ficha também aplica os limites do sistema. Ela não é salva se o personagem gastou mais pontos do que tinha ou tem mais escolas ou sonatas do que suas estatísticas permitem.

### Rolagem de dados

As rolagens usam reservas de dados de seis lados. Cada 5 ou 6 conta como sucesso. Os jogadores podem rolar:

- **Habilidades:** a habilidade e sua estatística vinculada, mais qualquer modificador da situação.
- **Magias:** uma rolagem de conjuração, mais um teste de coice que pode ferir o conjurador se a magia custar mais do que ele aguenta. Mana e vida são atualizadas automaticamente.
- **Itens e olhos místicos:** olhos místicos podem ter outro personagem como alvo.
- **Rolagens genéricas:** qualquer quantidade de dados, para todo o resto.

### Jogando juntos em tempo real

Cada rolagem é transmitida para toda a mesa na hora. Um histórico compartilhado mostra as 20 rolagens mais recentes: quem rolou, o retrato, o motivo da rolagem e o resultado. O app também acompanha quais personagens estão online, para que outros jogadores possam escolhê-los como alvo.

### Painel administrativo do mestre

Uma área administrativa separada permite ao mestre gerenciar todo o conteúdo do jogo: usuários, fichas de personagem, escolas e magias, sonatas e suas habilidades, milagres, escrituras, olhos místicos, efeitos, vantagens, itens e sistemas e subsistemas magiteck. Tudo o que é adicionado ali fica disponível para os jogadores nas lojas da ficha.

### Personalização

Os jogadores podem escolher entre vários temas visuais, incluindo claro, escuro, retrô, cyberpunk, pastel e floresta.

## Licença

Distribuído sob a [Licença MIT](LICENSE).
