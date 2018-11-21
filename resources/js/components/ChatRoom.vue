<template>
    <div class="chat-room" @click="selectRoom">
        <p>{{ room.name }}</p>
        <small>{{ room.last_message }}</small>
    </div>
</template>

<script>
    export default {
        props: ['room'],
        methods: {
            selectRoom() {
                this.$emit('selectroom', this.room);
            }
        },
    }
</script>

<style scoped>
    .chat-room:hover {
        background: #d3d9df;
        cursor: pointer;
    }

    .chat-room p {
        margin: 0;
    }
</style>
