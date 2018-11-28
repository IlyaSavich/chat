import swal from 'sweetalert';

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
    },
    methods: {
        addMessage(message) {
            const newMessage = { ...message, user: this.user };
            this.selectedRoom.messages.push(newMessage);
            this.selectedRoom.last_message = newMessage;

            axios.post('/room/' + this.selectedRoom.id + '/messages', newMessage);
        },
        selectRoom(room) {
            if (this.selectedRoom) {
                Echo.leave('room.' + this.selectedRoom.id);
            }

            this.selectedRoom = room;

            axios.get('/room/' + this.selectedRoom.id + '/messages').then(response => {
                this.selectedRoom.messages = response.data;
            });

            Echo.join('room.' + this.selectedRoom.id)
                .listen('MessagePosted', (e) => {
                    this.selectedRoom.messages.push({
                        message: e.message.message,
                        user: e.user,
                    });
                });
        },
        createRoom() {
            swal('What\'s the new chat name?', {
                content: 'input',
            }).then((name) => {
                axios.post('/rooms/create', { name }).then(() => {
                    this.fetchAllRooms().then(() => {
                        swal(`The room [${name}] created successfully.`);
                    });
                });
            });
        },
        fetchAllRooms() {
            return axios.get('/rooms').then(response => {
                this.rooms = response.data.map(room => ({ ...room, messages: [] }));
            });
        },
    },
    created() {
        this.fetchAllRooms().then(() => {
            this.selectRoom(this.rooms[0] || null);
        });
    },
});
