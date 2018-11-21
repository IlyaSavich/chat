<template lang="html">
    <div class="chat-message">
        <p>{{ message.user_name }}</p>
        <h5>{{ message.message }}</h5>
        <small>{{ new Date(Date.parse(message.created_at)).toLocaleTimeString() }}
        </small>
    </div>
</template>

<script>
    export default {
        props: ['message']
    }
</script>

<style lang="css">
    .chat-message {
        padding: 1rem;
    }

    .chat-message > p {
        margin-bottom: .5rem;
    }
</style>
