<script setup lang="ts">
import { ref } from "vue"
import ToastError from "@scripts/ToastError.ts";
import { useForm, client } from "laravel-precognition-vue";
import type { AxiosError } from "axios";

const emit = defineEmits(["success"]);

const modalRef = ref<HTMLDialogElement>();
const charClass = defineModel<string, string>("charClass");
const alignment = defineModel<string, string>("alignment");
const organization = defineModel<string, string>("organization");

client.use(window.axios);

const form = useForm("post", "/api/users", {
    login: "",
    password: "",
    sheet: {}
});

defineExpose({modalRef});

interface keyValue {
    key: string,
    value: number
}

interface user {
    login: string,
    password: string,
    sheet_id: number
}

function buildSheet() {
    let attributes: keyValue[] = [
        {
            "key": "health",
            "value": 0
        },
        {
            "key": "initiative",
            "value": 0
        },
        {
            "key": "movement",
            "value": 0
        }
    ]
    let stats: keyValue[] = [
        {
            "key": "strength",
            "value": 1
        },
        {
            "key": "dexterity",
            "value": 1
        },
        {
            "key": "agility",
            "value": 1
        },
        {
            "key": "endurance",
            "value": 1
        },
        {
            "key": "charisma",
            "value": 1
        },
        {
            "key": "perception",
            "value": 1
        },
        {
            "key": "intelligence",
            "value": 1
        }
    ];
    let items: Array<any> = [];
    let organizationField: string | null = null;
    let alignmentField: string | null = null;
    let scripture: any = null;
    if (charClass?.value == "mage") {
        if (alignment.value == undefined) {
            throw new ToastError("Falha ao definir alinhamento");
        }
        alignmentField = alignment.value;
        attributes.push(
            {
                "key": "mana",
                "value": 0
            }
        );
        stats.push(
            {
                "key": "magic",
                "value": 1
            }
        );
    }
    if (charClass?.value == "cleric") {
        if (organization.value == undefined) {
            throw new ToastError("Falha ao definir organização");
        }
        organizationField = organization.value;
        stats.push(
            {
                "key": "faith",
                "value": 1
            }
        );
        scripture = {
            "name": "Insira o nome da escritura",
            "creation_points": 0,
            "remaining_scripture_points": 0,
            "damage": 0,
            "range": 0,
            "sharpness": 0,
            "double": false,
            "strategy": null,
            "description": "Insira a descrição da escritura",
            "scripture_abilities": []
        };
    }
    if (charClass?.value == "magiteck") {
        stats.push(
            {
                "key": "tech",
                "value": 1
            }
        );
        attributes.push(
            {
                "key": "circuits",
                "value": 0
            }
        );
    }
    if (charClass?.value == "vampire") {
        stats.push(
            {
                "key": "lineage",
                "value": 1
            }
        );
        attributes.push(
            {
                "key": "blood_points",
                "value": 0
            },
            {
                "key": "blood_xp_animal",
                "value": 0
            },
            {
                "key": "blood_xp_human",
                "value": 0,
            },
            {
                "key": "blood_xp_vampire",
                "value": 0
            }
        );
    }
    if (charClass?.value == "mixed") {
        stats.push(
            {
                "key": "blood",
                "value": 1
            }
        );
    }
    let sheet = {
        "name": "Insira o nome do personagem",
        "description": "Insira a descrição física do personagem",
        "background": "Insira a história do personagem",
        "creation_points": 150,
        "portrait": "/storage/portraits/defaultPortrait.png",
        "alignment": alignmentField,
        "organization": organizationField,
        "stats": stats,
    "attributes": attributes,
    "skills": [
        {
            "key": "speed",
            "value": 0,
            "referenced_stat": "agility"
        },
        {
            "key": "acrobatics",
            "value": 0,
            "referenced_stat": "agility"
        },
        {
            "key": "melee",
            "value": 0,
            "referenced_stat": "strength"
        },
        {
            "key": "intimidation",
            "value": 0,
            "referenced_stat": "strength"
        },
        {
            "key": "ranged",
            "value": 0,
            "referenced_stat": "dexterity"
        },
        {
            "key": "stealth",
            "value": 0,
            "referenced_stat": "dexterity"
        },
        {
            "key": "conscience",
            "value": 0,
            "referenced_stat": "perception"
        },
        {
            "key": "investigation",
            "value": 0,
            "referenced_stat": "perception"
        },
        {
            "key": "wisdom",
            "value": 0,
            "referenced_stat": "intelligence"
        },
        {
            "key": "knowledge",
            "value": 0,
            "referenced_stat": "intelligence"
        },
        {
            "key": "medicine",
            "value": 0,
            "referenced_stat": "intelligence"
        },
        {
            "key": "survival",
            "value": 0,
            "referenced_stat": "endurance"
        },
        {
            "key": "tenacity",
            "value": 0,
            "referenced_stat": "endurance"
        },
        {
            "key": "diplomacy",
            "value": 0,
            "referenced_stat": "charisma"
        },
        {
            "key": "insight",
            "value": 0,
            "referenced_stat": "charisma"
        }
    ],
    "advantages": [],
    "blood": null,
    "effects": [],
    "items": items,
    "miracles": [],
    "mystic_eyes": [],
    "schools": [],
    "scripture": scripture,
    "sonatas": [],
    "systems": []
    }

    return sheet;
}

async function register() {
    form.sheet = buildSheet();
    modalRef.value?.close();

    form.submit().then(() => {
            emit("success");
        }
    ).catch((error: AxiosError) => {
        throw new ToastError("Falha ao registrar usuário", error);
    });
}

</script>

<template>
    <dialog class="modal" ref="modalRef" @keyup.enter="register()">
        <div class="modal-box w-11/12 max-w-5xl outline outline-primary overflow-auto">
            <h1 class="text-center text-2xl font-bold">Registrar</h1>
            <div class="h-5"></div>
            <form method="dialog" class="flex flex-col gap-5">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                <div class="outline outline-primary flex flex-col gap-5 p-5 rounded-xl">
                    <h1 class="text-center font-semibold text-xl">Usuário</h1>
                    <div>
                        <p v-if="form.errors['login'] != undefined" class=" text-sm text-[#ff0000] absolute -mt-5">{{ form.errors["login"] }}</p>
                        <label class="input input-bordered input-accent flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                class="h-4 w-4 opacity-70">
                                <path
                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" />
                            </svg>
                            <input type="text" class="grow" placeholder="Usuário" v-model="form.login" @change="form.validate('login')"/>
                        </label>
                    </div>
                    <div>
                        <p v-if="form.errors['password'] != undefined" class=" text-sm text-[#ff0000] absolute -mt-5">{{ form.errors["password"] }}</p>
                        <label class="input input-bordered input-accent flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                class="h-4 w-4 opacity-70">
                                <path
                                fill-rule="evenodd"
                                d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z"
                                clip-rule="evenodd" />
                            </svg>
                            <input id="password" type="password" class="grow" placeholder="Senha" v-model="form.password" @change="form.validate('password')"/>
                        </label>
                    </div>
                </div>
                <div class="outline outline-primary flex flex-col gap-5 p-5 rounded-xl">
                    <h1 class="text-center font-semibold text-xl">Classe</h1>
                    <div class="flex flex-row gap-32 justify-center form-control">
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Mago</span>
                            <input v-model="charClass" :value="'mage'" type="radio" name="class-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Clérigo</span>
                            <input v-model="charClass" :value="'cleric'" type="radio" name="class-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Vampiro</span>
                            <input v-model="charClass" :value="'vampire'" type="radio" name="class-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Mestico</span>
                            <input v-model="charClass" :value="'hybrid'" type="radio" name="class-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Magiteck</span>
                            <input v-model="charClass" :value="'magiteck'" type="radio" name="class-radio" class="radio radio-accent" />
                        </label>
                    </div>
                </div>
                <div v-if="charClass == 'mage'" class="outline outline-primary flex flex-col gap-5 p-5 rounded-xl">
                    <h1 class="text-center font-semibold text-xl">Alinhamento</h1>
                    <div class="flex flex-row gap-20 justify-center form-control">
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Fogo</span>
                            <input v-model="alignment" :value="'fire'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Água</span>
                            <input v-model="alignment" :value="'water'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Ar</span>
                            <input v-model="alignment" :value="'wind'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Terra</span>
                            <input v-model="alignment" :value="'earth'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Gelo</span>
                            <input v-model="alignment" :value="'ice'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Eletricidade</span>
                            <input v-model="alignment" :value="'electricity'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Vazio</span>
                            <input v-model="alignment" :value="'void'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Arcana</span>
                            <input v-model="alignment" :value="'arcana'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                    </div>
                </div>
                <div v-if="charClass == 'cleric'" class="outline outline-primary flex flex-col gap-5 p-5 rounded-xl">
                    <h1 class="text-center font-semibold text-xl">Organização</h1>
                    <div class="flex flex-row gap-20 justify-center form-control">
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Executores</span>
                            <input v-model="organization" :value="'executors'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Cavaleiros</span>
                            <input v-model="organization" :value="'chivalry'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Irmandade</span>
                            <input v-model="organization" :value="'brotherhood'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                        <label class="label flex flex-col gap-3">
                            <span class="label-text">Exorcistas</span>
                            <input v-model="organization" :value="'exorcists'" type="radio" name="alignment-radio" class="radio radio-accent" />
                        </label>
                    </div>

                </div>
                <div v-if="charClass == 'hybrid'" class="outline outline-primary flex flex-col gap-5 p-5 rounded-xl">

                </div>
                <button class="btn btn-outline btn-secondary" @click="register()">Confirmar</button>
            </form>
        </div>
    </dialog>
</template>
