<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

// Variável reativa para mostrar a pré-visualização da imagem antes de fazer upload
const avatarPreview = ref(null);

const form = useForm({
    _method: 'patch', // Disfarce necessário porque vamos enviar um ficheiro
    name: user.name,
    email: user.email,
    avatar: null,     // Novo campo
});

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    form.avatar = file;

    // Cria um link temporário no navegador para mostrar a pré-visualização
    if (file) {
        avatarPreview.value = URL.createObjectURL(file);
    } else {
        avatarPreview.value = null;
    }
};

const submit = () => {
    // Alterado de form.patch para form.post devido ao upload de ficheiros
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Limpa o ficheiro selecionado após o sucesso para evitar reenvios acidentais
            form.avatar = null;
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information, email address, and profile photo.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">

            <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    <img v-if="avatarPreview" :src="avatarPreview" class="h-16 w-16 object-cover rounded-full shadow" alt="Avatar Preview" />
                    <img v-else-if="user.avatar_url" :src="user.avatar_url" class="h-16 w-16 object-cover rounded-full shadow" alt="Current Avatar" />
                    <div v-else class="h-16 w-16 bg-gray-800 rounded-full flex items-center justify-center text-white font-bold text-xl uppercase shadow">
                        {{ user.name.charAt(0) }}
                    </div>
                </div>
                <label class="block">
                    <span class="sr-only">Choose profile photo</span>
                    <input
                        type="file"
                        @change="handleAvatarChange"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-gray-800 file:text-white
                        hover:file:bg-gray-700 transition-colors cursor-pointer"
                    />
                </label>
                <InputError class="mt-2" :message="form.errors.avatar" />
            </div>

            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>
                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
