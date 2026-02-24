<script setup>
import { ref, onMounted, computed } from 'vue'; // <-- AQUI ESTÁ A CORREÇÃO (computed importado)
import { Head, usePage, useForm } from '@inertiajs/vue3';
import ChatLayout from '@/Layouts/ChatLayout.vue';

const props = defineProps({
    currentRoom: Object,
    messages: Array,
});

const user = usePage().props.auth.user;

const form = useForm({
    body: '',
});

const messagesContainer = ref(null);

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

onMounted(() => scrollToBottom());

const submitMessage = () => {
    if (!form.body.trim()) return;
    alert("Vais enviar: " + form.body + "\n\n(A rota de envio será configurada a seguir!)");
    form.reset('body');
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const roomName = computed(() => {
    if (props.currentRoom.type === 'group') return props.currentRoom.name;
    const otherUser = props.currentRoom.users.find(u => u.id !== user.id);
    return otherUser ? otherUser.name : 'Unknown User';
});
</script>

<template>
    <Head :title="roomName" />
    <ChatLayout>
        <div class="h-14 flex items-center px-6 border-b border-gray-200 bg-white font-semibold shadow-sm z-10 flex-shrink-0">
            <span v-if="currentRoom.type === 'group'" class="text-gray-400 mr-2 text-xl leading-none">#</span>
            <span class="text-gray-800">{{ roomName }}</span>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-white" ref="messagesContainer">
            <div v-for="message in messages" :key="message.id" class="flex items-start group">
                <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex-shrink-0 flex items-center justify-center font-bold text-gray-600 mr-4">
                    {{ message.user.name.charAt(0) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-baseline mb-1">
                        <span class="font-bold text-sm mr-2 text-gray-900">{{ message.user.name }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ formatTime(message.created_at) }}</span>
                    </div>
                    <div class="text-gray-700 text-[15px] leading-relaxed whitespace-pre-wrap">
                        {{ message.body }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-gray-200 flex-shrink-0">
            <form @submit.prevent="submitMessage" class="flex relative max-w-4xl mx-auto">
                <input
                    v-model="form.body"
                    type="text"
                    placeholder="Type your message here..."
                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3.5 pr-20 shadow-sm"
                    autocomplete="off"
                />
                <button
                    type="submit"
                    class="absolute right-2 top-1.5 bottom-1.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium text-sm transition-colors disabled:opacity-50"
                    :disabled="form.processing || !form.body.trim()"
                >
                    Send
                </button>
            </form>
        </div>
    </ChatLayout>
</template>
