
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('chat-message', require('./components/ChatMessage.vue'));
Vue.component('chat-log', require('./components/ChatLog.vue'));
Vue.component('chat-composer', require('./components/ChatComposer.vue'));
Vue.component('chat-room-list', require('./components/ChatRoomList.vue'));
Vue.component('chat-room', require('./components/ChatRoom.vue'));

const app = new Vue({
    el: '#app',
    data: {
        rooms: [],
        selectedRoom: null,
        user: $('#app').data('user'),
        // usersInRoom: [],
    },
    methods: {
        addMessage(message) {
            const newMessage = { ...message, user_id: this.user.id, user_name: this.user.name };
            this.selectedRoom.messages.push(newMessage);
            this.selectedRoom.last_message = newMessage.message;

            axios.post('/room/' + this.selectedRoom.id + '/messages', newMessage);
        },
        selectRoom(room) {
            this.selectedRoom = room;

            axios.get('/room/' + this.selectedRoom.id + '/messages').then(response => {
                this.selectedRoom.messages = response.data;
                console.log(this.selectedRoom.messages[0]);
            });
        },
    },
    created() {
        axios.get('/rooms').then(response => {
            this.rooms = response.data.map(room => ({ ...room, messages: [] }));
        });

        // Echo.join('chatroom')
        //     .here((users) => {
        //         this.usersInRoom = users;
        //     })
        //     .joining((user) => {
        //         this.usersInRoom.push(user);
        //     })
        //     .leaving((user) => {
        //         this.usersInRoom = this.usersInRoom.filter(u => u != user);
        //     })
        //     .listen('MessagePosted', (e) => {
        //         this.messages.push({
        //             message: e.message.message,
        //             user: e.user
        //         });
        //     });
    },
});
