<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage, useForm, router } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const page = usePage();
const user = page.props.auth.user;
const rooms = computed(() => page.props.rooms || []);

const groupRooms = computed(() => rooms.value.filter(room => room.type === 'group'));
const directRooms = computed(() => rooms.value.filter(room => room.type === 'direct'));

const showUserMenu = ref(false);

const getRoomName = (room) => {
    if (room.type === 'group') return room.name;
    if (room.type === 'direct' && room.users) {
        const otherUser = room.users.find(u => u.id !== user.id);
        return otherUser ? otherUser.name : 'Unknown User';
    }
    return 'Chat';
};

const activeRoomDropdown = ref(null);
const dropdownPos = ref({ top: '0px', left: '0px' });

const activeRoom = computed(() => rooms.value.find(r => r.id === activeRoomDropdown.value));

const toggleRoomDropdown = (event, id) => {
    if (activeRoomDropdown.value === id) {
        activeRoomDropdown.value = null;
    } else {
        const rect = event.currentTarget.getBoundingClientRect();
        dropdownPos.value = {
            top: `${rect.top}px`,
            left: `${rect.right + 8}px`
        };
        activeRoomDropdown.value = id;
    }
};

const closeRoomDropdown = () => {
    activeRoomDropdown.value = null;
};

const exitRoom = (id) => {
    if (confirm('Are you sure you want to exit this room? You will no longer receive messages.')) {
        router.delete(route('chat.leave', id), {
            preserveScroll: true,
            onSuccess: () => closeRoomDropdown()
        });
    }
};

const confirmingRoomDeletion = ref(false);
const roomToDelete = ref(null);
const deleteForm = useForm({});

const confirmRoomDeletion = (id) => {
    roomToDelete.value = rooms.value.find(r => r.id === id);
    confirmingRoomDeletion.value = true;
    closeRoomDropdown();
};

const deleteRoom = () => {
    if (!roomToDelete.value) return;

    deleteForm.delete(route('admin.rooms.destroy', roomToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingRoomDeletion.value = false;
    setTimeout(() => { roomToDelete.value = null; }, 200);
    deleteForm.reset();
};

onMounted(() => {
    document.addEventListener('click', closeRoomDropdown);
    window.addEventListener('scroll', closeRoomDropdown, true);
});

onUnmounted(() => {
    document.removeEventListener('click', closeRoomDropdown);
    window.removeEventListener('scroll', closeRoomDropdown, true);
});

</script>

<template>
    <div class="flex h-screen overflow-hidden bg-white text-gray-900 font-sans antialiased">

        <div class="w-64 flex-shrink-0 border-r border-gray-200 bg-[#f9f9f9] flex flex-col">
            <div class="h-14 flex items-center px-4 font-bold text-lg border-b border-gray-200 cursor-default text-gray-800">
                <Link :href="route('chat.index')" class="flex flex-row">
                    <ApplicationLogo class="w-6 h-6 mr-2 text-blue-600" />
                    <span class="tracking-tight">InovChat</span>
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-6" @scroll="closeRoomDropdown">

                <div>
                    <h3 class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rooms</h3>

                    <div v-if="groupRooms.length > 0" class="space-y-0.5">
                        <div v-for="room in groupRooms" :key="room.id" class="relative group mb-1">
                            <div
                                class="flex items-center justify-between px-3 py-1.5 text-sm font-medium rounded-lg transition-colors cursor-pointer"
                                :class="route().current('chat.show', room.id) ? 'bg-gray-200 text-gray-900' : 'text-gray-700 hover:bg-gray-200/50 hover:text-gray-900'"
                            >
                                <Link :href="route('chat.show', room.id)" class="flex-1 truncate flex items-center">
                                    <span class="mr-2 text-gray-400 text-lg leading-none">#</span>
                                    <span class="truncate">{{ getRoomName(room) }}</span>
                                </Link>

                                <button
                                    @click.stop="toggleRoomDropdown($event, room.id)"
                                    class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-300 transition-colors focus:outline-none flex-shrink-0 ml-1"
                                    :class="{ 'opacity-100': activeRoomDropdown === room.id, 'opacity-0 group-hover:opacity-100': activeRoomDropdown !== room.id }"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="mx-2 px-3 py-3 border border-dashed border-gray-300 rounded-xl bg-gray-50/50">
                        <p class="text-[11px] text-gray-500 text-center leading-relaxed">
                            No rooms joined yet.
                        </p>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between px-3 mb-2">
                        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pings</h3>
                        <Link :href="route('chat.direct.new')" class="text-gray-400 hover:text-indigo-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </Link>
                    </div>

                    <div v-if="directRooms.length > 0" class="space-y-0.5">
                        <div v-for="room in directRooms" :key="room.id" class="relative group mb-1">
                            <div
                                class="flex items-center justify-between px-3 py-1.5 text-sm font-medium rounded-lg transition-colors cursor-pointer"
                                :class="route().current('chat.show', room.id) ? 'bg-gray-200 text-gray-900' : 'text-gray-700 hover:bg-gray-200/50 hover:text-gray-900'"
                            >
                                <Link :href="route('chat.show', room.id)" class="flex-1 truncate flex items-center">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] mr-2 font-bold flex-shrink-0"
                                         :class="route().current('chat.show', room.id) ? 'bg-blue-200 text-blue-700' : 'bg-blue-100 text-blue-600'">
                                        DM
                                    </div>
                                    <span class="truncate">{{ getRoomName(room) }}</span>
                                </Link>

                                <button
                                    @click.stop="toggleRoomDropdown($event, room.id)"
                                    class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-300 transition-colors focus:outline-none flex-shrink-0 ml-1"
                                    :class="{ 'opacity-100': activeRoomDropdown === room.id, 'opacity-0 group-hover:opacity-100': activeRoomDropdown !== room.id }"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="mx-2 px-3 py-3 border border-dashed border-gray-300 rounded-xl bg-gray-50/50">
                        <p class="text-[11px] text-gray-500 text-center leading-relaxed">
                            No active conversations.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-3 border-t border-gray-200 relative">
                <div v-if="showUserMenu" @click="showUserMenu = false" class="fixed inset-0 z-40"></div>

                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 translate-y-2"
                    enter-to-class="transform opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 translate-y-0"
                    leave-to-class="transform opacity-0 translate-y-2"
                >
                    <div v-if="showUserMenu" class="absolute bottom-full left-0 mb-2 w-full px-3 z-50">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden py-1">
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                Profile
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">
                                Log out
                            </Link>
                        </div>
                    </div>
                </transition>

                <template v-if="$page.props.auth.user.is_admin">
                    <Link :href="route('admin.rooms.create')" class="flex items-center w-full p-2 mb-1 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-200/75 transition-colors focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Create a Room
                    </Link>

                    <Link :href="route('admin.users.index')" class="flex items-center w-full p-2 mb-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-200/75 transition-colors focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Manage Users
                    </Link>
                </template>

                <button
                    @click="showUserMenu = !showUserMenu"
                    class="flex items-center space-x-2 w-full hover:bg-gray-200/75 p-2 rounded-lg transition-colors focus:outline-none"
                    :class="{'bg-gray-200/75': showUserMenu}"
                >
                    <img
                        v-if="$page.props.auth.user.avatar_url"
                        :src="$page.props.auth.user.avatar_url"
                        class="w-7 h-7 rounded-full object-cover shadow-sm flex-shrink-0"
                        alt="User Avatar"
                    />
                    <span
                        v-else
                        class="w-7 h-7 rounded-full bg-gray-800 flex items-center justify-center text-xs font-bold text-white uppercase flex-shrink-0"
                    >
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </span>
                    <span class="flex-1 truncate text-sm font-medium text-gray-700 text-left">
                        {{ $page.props.auth.user.name }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': showUserMenu}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-white">
            <slot />
        </div>
    </div>

    <Teleport to="body">
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="activeRoom"
                class="fixed w-36 bg-white rounded-md shadow-xl border border-gray-200 z-[9999] overflow-hidden"
                :style="{ top: dropdownPos.top, left: dropdownPos.left }"
                @click.stop
            >
                <div class="py-1">
                    <template v-if="activeRoom.type === 'group'">
                        <template v-if="$page.props.auth.user.is_admin">
                            <Link :href="route('admin.rooms.edit', activeRoom.id)" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-indigo-600 transition-colors">
                                Edit Room
                            </Link>
                            <Link :href="route('admin.rooms.participants.index', activeRoom.id)" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 hover:text-indigo-600 transition-colors">
                                Manage Members
                            </Link>
                            <button @click="confirmRoomDeletion(activeRoom.id)" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                Delete Room
                            </button>
                        </template>

                        <template v-else>
                            <button @click="exitRoom(activeRoom.id)" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                Exit Room
                            </button>
                        </template>
                    </template>

                    <template v-else-if="activeRoom.type === 'direct'">
                        <button @click="confirmRoomDeletion(activeRoom.id)" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                            Delete Chat
                        </button>
                    </template>
                </div>
            </div>
        </transition>
    </Teleport>

    <Modal :show="confirmingRoomDeletion" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Are you sure you want to delete <span class="font-bold">{{ roomToDelete ? getRoomName(roomToDelete) : 'this chat' }}</span>?
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                This action will permanently delete the room and all of its messages. This action cannot be undone.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                <DangerButton
                    :class="{ 'opacity-25': deleteForm.processing }"
                    :disabled="deleteForm.processing"
                    @click="deleteRoom"
                >
                    Delete Chat
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
