@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">Chats
                        <button @click="createRoom" class="float-right btn btn-primary btn-sm">+</button>
                    </div>
                    <div class="card-body">
                        <chat-room-list :rooms="rooms" :selected-room="selectedRoom" v-on:selectroom="selectRoom"></chat-room-list>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">
                        @{{ selectedRoom ? selectedRoom.name : "Chatroom" }}
                        <span class="badge badge-dark" v-show="selectedRoom && selectedRoom.type !== 'dialog'">
                            @{{ selectedRoom ? selectedRoom.users_count : 0 }}
                        </span>
                        <i class="badge badge-danger float-right chat-delete"
                           v-show="selectedRoom && selectedRoom.type !== 'dialog' && selectedRoom.owner_id === user.id"
                           v-on:click="deleteRoom">X</i>
                    </div>

                    <div class="card-body">
                        <chat-log :messages="selectedRoom ? selectedRoom.messages : []" :user="user"></chat-log>
                        <chat-composer v-on:messagesent="addMessage"></chat-composer>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
