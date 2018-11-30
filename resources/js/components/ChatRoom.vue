<template>
    <div class="chat-room" v-bind:class="{ active: selected }" @click="selectRoom">
        <div v-if="room.unread" class="badge badge-info float-right">!</div>
        <p class="title">{{ room.name }}</p>
        <p v-if="room.last_message" class="message-preview">{{ room.last_message.user.name + ': ' + room.last_message.message }}</p>
        <i v-else class="message-preview">No messages yet!</i>
    </div>
</template>

<script>
    export default {
        props: ['room', 'selected'],
        methods: {
            selectRoom() {
                this.$emit('selectroom', this.room);
            }
        },
    }
</script>

<style scoped>
    .chat-room {
        padding: 5px 10px;
    }

    .chat-room.active {
        background-color: rgba(0, 0, 0, 0.125);
    }

    .chat-room:hover {
        background: #ccc;
        cursor: pointer;
    }

    .chat-room .title {
        font-size: 16px;
        margin: 0;
    }

    .chat-room .message-preview {
        font-size: 12px;
        margin-left: 20px;
    }
</style>
