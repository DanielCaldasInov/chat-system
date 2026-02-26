<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const page = usePage();
const user = page.props.auth.user;
const rooms = computed(() => page.props.rooms);

const groupRooms = computed(() => rooms.value.filter(room => room.type === 'group'));
const directRooms = computed(() => rooms.value.filter(room => room.type === 'direct'));

const showUserMenu = ref(false);

/**
 * @param {{ type: string, name: string|null, users: Array<{id: number, name: string}>|undefined }} room
 */
const getRoomName = (room) => {
    if (room.type === 'group') return room.name;
    if (room.type === 'direct' && room.users) {
        const otherUser = room.users.find(u => u.id !== user.id);
        return otherUser ? otherUser.name : 'Unknown User';
    }
    return 'Chat';
};
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-white text-gray-900 font-sans antialiased">

        <div class="w-64 flex-shrink-0 border-r border-gray-200 bg-[#f9f9f9] flex flex-col">
            <div class="h-14 flex items-center px-4 font-bold text-lg border-b border-gray-200 cursor-default text-gray-800">
                <Link :href="route('chat.index')"
                      class="flex flex-row">
                    <ApplicationLogo class="w-6 h-6 mr-2 text-blue-600" />
                    <span class="tracking-tight">InovChat</span>
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-6">
                <div>
                    <h3 class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rooms</h3>
                    <div class="space-y-0.5">
                        <Link
                            v-for="room in groupRooms"
                            :key="room.id"
                            :href="route('chat.show', room.id)"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                            :class="route().current('chat.show', room.id) ? 'bg-gray-200 text-gray-900' : 'text-gray-700 hover:bg-gray-200/50 hover:text-gray-900'"
                        >
                            <span class="mr-2 text-gray-400 text-lg leading-none">#</span>
                            <span class="truncate">{{ getRoomName(room) }}</span>
                        </Link>
                    </div>
                </div>

                <div>
                    <h3 class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Pings</h3>
                    <div class="space-y-0.5">
                        <Link
                            v-for="room in directRooms"
                            :key="room.id"
                            :href="route('chat.show', room.id)"
                            class="flex items-center px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                            :class="route().current('chat.show', room.id) ? 'bg-gray-200 text-gray-900' : 'text-gray-700 hover:bg-gray-200/50 hover:text-gray-900'"
                        >
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] mr-2 font-bold flex-shrink-0"
                                 :class="route().current('chat.show', room.id) ? 'bg-blue-200 text-blue-700' : 'bg-blue-100 text-blue-600'">
                                DM
                            </div>
                            <span class="truncate">{{ getRoomName(room) }}</span>
                        </Link>
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
                            <Link
                                :href="route('profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                            >
                                Profile
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium"
                            >
                                Log out
                            </Link>
                        </div>
                    </div>
                </transition>
                <Link
                    v-if="$page.props.auth.user.is_admin"
                    :href="route('admin.users.index')"
                    class="flex items-center w-full p-2 mb-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-200/75 transition-colors focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manage Users
                </Link>
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
</template>
