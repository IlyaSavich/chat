<template>
    <div class="chat-room-list">
        <chat-room v-for="room in rooms" :room="room" :selected="selectedRoom && room.id === selectedRoom.id" :key="room.id" v-on:selectroom="selectRoom"></chat-room>
        <div class="empty" v-show="rooms.length === 0">
            No rooms yet!
        </div>
    </div>
</template>

<script>
    export default {
        props: ['rooms', 'selectedRoom'],
        methods: {
            selectRoom(room) {
                this.$emit('selectroom', room);
            }
        },
    }
</script>

<style lang="css">

</style>
