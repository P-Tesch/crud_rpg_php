<script setup>
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"
import RegisterModal from './components/modals/registerModal.vue';
import SuccessToast from './components/alerts/successToast.vue';

const form = reactive({
    login: null,
    password: null,
    remember_me: null
})

const registerModal = ref(null);
const successToast = ref(null);

const auth = () => {
    router.get("/api/login", form)
}

function success() {
    this.successToast.toastRef = true;
    setTimeout(() => this.successToast.toastRef = false, 2500);
}

</script>

<template>
    <Head title="Login" />
    <div class="flex h-screen">
        <div class="flex flex-col border border-1 rounded-md border-primary p-5 w-4/12 h-4/12 m-auto gap-2">
            <h1 class="text-xl font-bold text-center mb-4 text-accent">Gerenciador de fichas</h1>
            <form class="flex flex-col gap-4" @submit.prevent="submit">
                <div>
                    <label class="input input-bordered input-accent flex items-center gap-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="h-4 w-4 opacity-70">
                            <path
                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM12.735 14c.618 0 1.093-.561.872-1.139a6.002 6.002 0 0 0-11.215 0c-.22.578.254 1.139.872 1.139h9.47Z" />
                        </svg>
                        <input id="form-user" type="text" class="grow" placeholder="Usuário" v-model="form.login" />
                    </label>
                </div>
                <div>
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
                        <input id="form-password" type="password" class="grow" placeholder="Senha" v-model="form.password" />
                    </label>
                </div>
                <div class="flex flex-row gap-2 items-center">
                    <input  if="form-remember-me" class="checkbox checkbox-accent" type="checkbox" id="remember_me" v-model="form.remember_me">
                    <label class="label-text" for="remember_me">Lembre-me</label>
                </div>
            </form>
            <div class="flex flex-row">
                <button class="btn btn-outline btn-accent mx-auto" v-on:click="auth">Entrar</button>
                <button class="btn btn-outline btn-secondary mx-auto" v-on:click="registerModal.modalRef.showModal()">Registrar</button>
            </div>
        </div>
    </div>
    <Teleport to="body">
        <RegisterModal @success="success()" ref="registerModal"/>
    </Teleport>
    <SuccessToast class="z-10" ref="successToast" :message="'Registrado com sucesso'" />
</template>
