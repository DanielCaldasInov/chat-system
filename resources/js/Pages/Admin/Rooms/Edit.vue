<script setup>
import ChatLayout from '@/Layouts/ChatLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    room: Object,
});

// Nota: Para fazer upload de imagens numa rota PATCH no Laravel + Inertia,
// precisamos usar form.post e passar _method: 'patch'.
const form = useForm({
    _method: 'patch',
    name: props.room.name,
    avatar: null,
});

const imagePreview = ref(props.room.avatar); // Se já tiver imagem, mostra-a

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.avatar = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.rooms.update', props.room.id), {
        preserveScroll: true,
    });
};

// Lógica de Apagar Sala
const confirmingRoomDeletion = ref(false);
const deleteForm = useForm({});

const confirmRoomDeletion = () => {
    confirmingRoomDeletion.value = true;
};

const deleteRoom = () => {
    deleteForm.delete(route('admin.rooms.destroy', props.room.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingRoomDeletion.value = false;
    deleteForm.reset();
};
</script>

<template>
    <Head title="Edit Room" />

    <ChatLayout>
        <div class="h-full overflow-y-auto bg-gray-50">
            <div class="max-w-4xl mx-auto py-12 px-6 lg:px-8">

                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <Link
                            :href="route('chat.show', room.id)"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-2 inline-flex items-center transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Chat
                        </Link>
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                            Edit Room
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Update the room name and image settings.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 max-w-2xl">
                        <form @submit.prevent="submit" class="space-y-6">

                            <div>
                                <InputLabel for="name" value="Room Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel value="Room Avatar" />
                                <div class="mt-2 flex items-center space-x-4">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-gray-200">
                                        <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover"  alt="Image"/>
                                        <svg v-else class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <label class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <span>Change Avatar</span>
                                        <input type="file" class="sr-only" accept="image/*" @change="handleImageUpload" />
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.avatar" />
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                                <div>
                                    <button
                                        type="button"
                                        @click="confirmRoomDeletion"
                                        class="text-sm text-red-600 hover:text-red-900 font-medium transition-colors"
                                    >
                                        Delete Room
                                    </button>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <Link :href="route('chat.show', room.id)" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors">
                                        Cancel
                                    </Link>

                                    <PrimaryButton
                                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                    >
                                        <span v-if="form.processing">Processing...</span>
                                        <span v-else>Save Changes</span>
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="h-12"></div>
            </div>
        </div>

        <Modal :show="confirmingRoomDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete <span class="font-bold">{{ room.name }}</span>?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    This action will permanently delete the room and all of its messages. This cannot be undone.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': deleteForm.processing }"
                        :disabled="deleteForm.processing"
                        @click="deleteRoom"
                    >
                        Delete Room
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </ChatLayout>
</template>
