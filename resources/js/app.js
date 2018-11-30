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
            const newMessage = {...message, user: this.user, created_at: new Date().toISOString()};
            this.addMessageToRoom(this.selectedRoom, newMessage);

            axios.post('/room/' + this.selectedRoom.id + '/messages', {...newMessage, user_id: this.user.id});
        },
        selectRoom(room) {
            if (this.selectedRoom && this.selectedRoom.id === room.id) {
                return;
            }

            this.selectedRoom = room;
            this.selectedRoom.unread = false;

            axios.get('/room/' + this.selectedRoom.id + '/messages').then(response => {
                this.selectedRoom.messages = response.data;
            });
        },
        fetchAllRooms() {
            return axios.get('/rooms').then(response => {
                this.rooms = response.data.map(room => ({...room, messages: []}));
            });
        },
        createRoom() {
            swal('What\'s the name for new chat?', {
                content: 'input',
            }).then((name) => {
                axios.post('/rooms/create', {name}).then(({data}) => {
                    this.fetchAllRooms().then(() => {
                        this.selectRoom(this.rooms.find((room) => room.id === data.room.id));
                        swal(`The room ${name} created successfully.`);
                    });
                });
            });
        },
        deleteRoom() {
            if (!this.selectedRoom) {
                return;
            }

            swal({
                title: 'Are you sure?',
                text: 'Once deleted, you will not be able to recover this room!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(() =>
                axios.delete('/room/' + this.selectedRoom.id).then(() => {
                    this.deleteRoomFromList(this.selectedRoom);
                    swal(`The room ${name} deleted successfully.`);
                }),
            );
        },
        deleteRoomFromList(deletedRoom) {
            this.rooms = this.rooms.filter((room) => room.id !== deletedRoom.id);
            if (this.selectedRoom && this.selectedRoom.id === deletedRoom.id) {
                this.selectedRoom = this.rooms[0];
            }
        },
        addMessageToRoom(room, message) {
            room.messages.push(message);
            room.last_message = message;
        },
    },
    created() {
        this.fetchAllRooms().then(() => {
            if (this.rooms.length > 0) {
                this.selectRoom(this.rooms[0]);
            }
        });

        Echo.join('user.registration')
            .listen('UserRegistered', (e) => {
                this.fetchAllRooms().then(() => {
                    swal(`${e.user.name} has joined the chat.`);
                });
            });

        Echo.join('rooms')
            .listen('RoomCreated', (e) => {
                const room = e.room;

                this.rooms.push(room);
                swal(`Room ${room.name} has been created.`);
            })
            .listen('RoomDeleted', (e) => {
                const roomId = e.roomId;
                const room = this.rooms.find((room) => room.id === roomId);

                this.deleteRoomFromList(room);
                swal(`Room ${room.name} has been deleted.`);
            })
            .listen('MessagePosted', (e) => {
                const messagedRoom = this.rooms.find((room) => room.id === e.room.id);

                this.addMessageToRoom(messagedRoom, e.message);

                if (messagedRoom.id !== this.selectedRoom.id) {
                    messagedRoom.unread = true;
                }
            });
    },
});
