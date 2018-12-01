# Installation

## Install development environment
Homestead Vagrant box is used in the project for development environment.
So the first we need to install Vagrant.

#### Download & install VitualBox
https://www.virtualbox.org/wiki/Downloads

#### Download & install Vagrant
https://www.vagrantup.com/downloads.html

#### Install & Setup Homestead
After Vagrant installation you can go to Homestead installation. Following the documentation you need to install and configure your application in Homestead. 

https://laravel.com/docs/5.7/homestead#installation-and-setup

After the installation in your Homestead directory run

    vagrant up

**Note!** You need to create folder which will be used as a project folder before will up your homestead. 

## Install project

### Clone repository
Find a place where you want to store project and clone repository

    git clone git@github.com:IlyaSavich/chat.git

### Install project packages
To connect vagrant via ssh in your homestead directory run

    vagrant ssh

Then go to project directory into vagrant box, e.g. `~/chat`

    cd ~/chat

And install php packages

    composer install

And node modules

    npm install

**Note** if there is some problems with npm will appear then you can try to install npm locally and install these modules locally and use npm locally.

### Install Laravel App
Laravel Framework is used in the project as framework for backend app.
Vue.js is used for frontend app.

##### Create `.env` file copy from `.env.example`
And in `.env` configure connection to DB, e.g.

    DB_DATABASE=chat # DB name from Homestead.yml
    DB_USERNAME=homestead # default credentials
    DB_PASSWORD=secret

In project directory in vagrant run

    artisan key:generate (or php artisan key:generate)

##### Configure Pusher WebSocket server
Pusher is used as a websocket server in the project.
So first you need to configure driver for broadcasting in `.env`

    BROADCAST_DRIVER=pusher

Also need to configure credentials of Pusher account

    PUSHER_APP_ID=642196
    PUSHER_APP_KEY=c41184cc19fe08f24b71
    PUSHER_APP_SECRET=1670ba38642f59d151fa
    PUSHER_APP_CLUSTER=eu
    
    MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

##### Setup database
In project directory in vagrant run

    artisan migrate

##### Build frontend app

    npm run

For development you can run watcher

    npm run watch

### Start using the chat application!

# Usage

For daily usage you only need to run homestead box in your Homestead directory

    vagrant up
