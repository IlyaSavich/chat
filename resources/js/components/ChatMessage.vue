<template lang="html">
    <div class="chat-message" v-bind:class="{ active: user.id === message.user.id }">
        <p>{{ message.user.name }}</p>
        <h5>{{ message.message }}</h5>
        <small>{{ new Date(Date.parse(message.created_at)).toLocaleTimeString() }}
        </small>
    </div>
</template>

<script>
    export default {
        props: ['message', 'user']
    }
</script>

<style lang="css">
    .chat-message {
        padding: 1rem;
    }

    .chat-message:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.125);
    }

    .chat-message > p {
        margin-bottom: .5rem;
    }
</style>
