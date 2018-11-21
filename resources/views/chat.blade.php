@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-default">
                    <div class="card-header">Chats</div>
                    <div class="card-body">
                        <chat-room-list :rooms="rooms" v-on:selectroom="selectRoom"></chat-room-list>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">
                        @{{ selectedRoom ? selectedRoom.name : "Chatroom" }}
                        <span class="badge badge-dark float-right" v-show="selectedRoom && selectedRoom.type !== 'dialog'">
                            @{{ selectedRoom ? selectedRoom.users.length : 0 }}
                        </span>
                    </div>

                    <div class="card-body">
                        <chat-log :messages="selectedRoom ? selectedRoom.messages : []"></chat-log>
                        <chat-composer v-on:messagesent="addMessage"></chat-composer>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
