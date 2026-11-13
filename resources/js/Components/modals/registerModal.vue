<script setup lang="ts">
import { ref } from "vue"
import ToastError from "@scripts/ToastError.ts";
import BaseModal from "@ui/BaseModal.vue";
import Icon from "@ui/Icon.vue";
import { useForm, client, axiosAdapter } from "laravel-precognition-vue";

const emit = defineEmits(["success"]);

const modalRef = ref<HTMLDialogElement>();
const charClass = defineModel<string, string>("charClass");
const alignment = defineModel<string, string>("alignment");
const organization = defineModel<string, string>("organization");

client.useHttpClient(axiosAdapter(window.axios));

const form = useForm("post", "/api/users", {
    login: "",
    password: "",
    sheet: {}
});

defineExpose({modalRef});

type RadioOption = {
    value: string;
    label: string;
};

const classOptions: RadioOption[] = [
    { value: "mage", label: "Mago" },
    { value: "cleric", label: "Clérigo" },
    { value: "vampire", label: "Vampiro" },
    { value: "hybrid", label: "Mestiço" },
    { value: "magiteck", label: "Magiteck" },
];

const alignmentOptions: RadioOption[] = [
    { value: "fire", label: "Fogo" },
    { value: "water", label: "Água" },
    { value: "wind", label: "Ar" },
    { value: "earth", label: "Terra" },
    { value: "ice", label: "Gelo" },
    { value: "electricity", label: "Eletricidade" },
    { value: "void", label: "Vazio" },
    { value: "arcana", label: "Arcana" },
];

const organizationOptions: RadioOption[] = [
    { value: "executors", label: "Executores" },
    { value: "chivalry", label: "Cavaleiros" },
    { value: "brotherhood", label: "Irmandade" },
    { value: "exorcists", label: "Exorcistas" },
];

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
    ).catch((error) => {
        throw new ToastError("Falha ao registrar usuário", error);
    });
}

</script>

<template>
    <dialog class="modal" ref="modalRef" @keyup.enter="register()">
        <BaseModal title="Criar conta" subtitle="Novo personagem" size="md">
            <form method="dialog" class="flex flex-col gap-6">
                <fieldset class="flex flex-col gap-3">
                    <legend class="label-mono mb-3">Usuário</legend>
                    <div>
                        <label class="input w-full" :class="{ 'input-error': form.errors['login'] != undefined }">
                            <Icon name="user" class="text-base-content/50" />
                            <input type="text" class="grow" placeholder="Usuário" v-model="form.login" @change="form.validate('login')"/>
                        </label>
                        <p v-if="form.errors['login'] != undefined" class="mt-1 text-xs text-error">{{ form.errors["login"] }}</p>
                    </div>
                    <div>
                        <label class="input w-full" :class="{ 'input-error': form.errors['password'] != undefined }">
                            <Icon name="key" class="text-base-content/50" />
                            <input id="password" type="password" class="grow" placeholder="Senha" v-model="form.password" @change="form.validate('password')"/>
                        </label>
                        <p v-if="form.errors['password'] != undefined" class="mt-1 text-xs text-error">{{ form.errors["password"] }}</p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="label-mono mb-3">Classe</legend>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-5">
                        <label v-for="option in classOptions" :key="option.value" class="cursor-pointer">
                            <input v-model="charClass" :value="option.value" type="radio" name="class-radio" class="peer sr-only" />
                            <span class="block rounded-field border border-base-content/12 px-3 py-2 text-center text-sm transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline-2 peer-focus-visible:outline-primary hover:border-base-content/30">{{ option.label }}</span>
                        </label>
                    </div>
                </fieldset>

                <fieldset v-if="charClass == 'mage'">
                    <legend class="label-mono mb-3">Alinhamento</legend>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <label v-for="option in alignmentOptions" :key="option.value" class="cursor-pointer">
                            <input v-model="alignment" :value="option.value" type="radio" name="alignment-radio" class="peer sr-only" />
                            <span class="block rounded-field border border-base-content/12 px-3 py-2 text-center text-sm transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline-2 peer-focus-visible:outline-primary hover:border-base-content/30">{{ option.label }}</span>
                        </label>
                    </div>
                </fieldset>

                <fieldset v-if="charClass == 'cleric'">
                    <legend class="label-mono mb-3">Organização</legend>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <label v-for="option in organizationOptions" :key="option.value" class="cursor-pointer">
                            <input v-model="organization" :value="option.value" type="radio" name="organization-radio" class="peer sr-only" />
                            <span class="block rounded-field border border-base-content/12 px-3 py-2 text-center text-sm transition-colors peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary peer-focus-visible:outline-2 peer-focus-visible:outline-primary hover:border-base-content/30">{{ option.label }}</span>
                        </label>
                    </div>
                </fieldset>

                <button class="btn btn-primary" @click="register()">Criar conta</button>
            </form>
        </BaseModal>
    </dialog>
</template>
