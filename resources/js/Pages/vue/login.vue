<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive } from "vue"
import RegisterModal from '@modals/registerModal.vue';
import ToastHandler from '@components/toastHandler.vue';
import GuestLayout from '@layouts/GuestLayout.vue';
import BrandMark from '@ui/BrandMark.vue';
import Icon from '@ui/Icon.vue';

const form = reactive({
    login: null,
    password: null,
    remember_me: null
})

const registerModal = ref<InstanceType<typeof RegisterModal>>();
const toastHandler = ref<InstanceType<typeof ToastHandler>>();

const auth = () : void => {
    router.get("/api/login", form);
}

function success() : void {
    toastHandler.value?.showSuccess("Registrado com sucesso");
}

</script>

<template>
    <Head title="Login" />
    <GuestLayout>
        <div class="flex flex-col items-center gap-3 pb-8 text-center">
            <BrandMark class="size-14" />
            <h1 class="font-display text-2xl font-semibold tracking-tight">Gerenciador de fichas</h1>
        </div>

        <section class="hud-frame chamfer p-px">
            <div class="chamfer bg-base-200/90 p-6 backdrop-blur-sm">
                <form class="flex flex-col gap-4" @submit.prevent="auth">
                    <label class="input w-full">
                        <Icon name="user" class="text-base-content/50" />
                        <input id="form-user" type="text" class="grow" placeholder="Usuário" autocomplete="username" v-model="form.login" />
                    </label>
                    <label class="input w-full">
                        <Icon name="key" class="text-base-content/50" />
                        <input id="form-password" type="password" class="grow" placeholder="Senha" autocomplete="current-password" v-model="form.password" />
                    </label>
                    <label for="remember_me" class="flex cursor-pointer items-center gap-2 text-sm text-base-content/75">
                        <input id="remember_me" class="checkbox checkbox-sm checkbox-primary" type="checkbox" v-model="form.remember_me">
                        Lembre-me
                    </label>
                    <button type="submit" class="btn btn-primary mt-2 w-full">Entrar</button>
                </form>
            </div>
        </section>

        <p class="mt-6 text-center text-sm text-base-content/60">
            Ainda não tem uma conta?
            <button type="button" class="link font-medium text-secondary link-hover" @click="registerModal?.modalRef?.showModal()">Registrar</button>
        </p>
    </GuestLayout>
    <Teleport to="body">
        <RegisterModal @success="success()" ref="registerModal"/>
    </Teleport>
    <Teleport to="body">
        <ToastHandler ref="toastHandler" />
    </Teleport>
</template>
