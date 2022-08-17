<template>
    <div>
        <div class="rooms">
            <div class="my-user-detail">
                <img src="/images/avatar.png" alt="">
                <button class="btn btn-sm btn-info" @click="openModal=true">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search User" v-model="search">
            </div>
            <div class="rooms-list scrollbar">
                <div v-if="!loading">
                    <div class="room-list" v-for="room in rooms" :key="room.id">
                        <router-link :to="'/chat/'+room.user_id">
                        <div class="media">
                            <img class="mr-2" :src="room.avatar" alt="Generic placeholder image">
                            <div class="media-body">
                                <h6 class="mt-0">
                                    <span class="font-weight-bold">
                                        {{room.name}}
                                    </span>
                                    <span class="float-right">
                                        {{room.time | moment}}
                                    </span>
                                </h6>
                                <div class="last-message">
                                    <p class="text-truncate">
                                        {{room.message}}
                                    </p>
                                    <span class="badge badge-success message-count" v-if="room.unread>0">
                                        {{room.unread}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        </router-link>
                    </div>
                </div>
                <div v-else>
                    <div class="text-loading">
                        Loading....
                    </div>
                </div>
            </div>
        </div>
        <div id="new-chat" v-if="openModal">
            <div class="new-chat-modal">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold">Start chat</h6>
                    <button class="btn-danger btn-sm" @click="openModal=false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <li class="list-group-item mt-3">
                    <input type="search" class="form-control" v-model="search_users" placeholder="Search..." @keyup="loadUsers()">
                </li>
                <div class="list-group chat-users-list scrollbar">
                    <li v-for="user in users" :key="user.id" class="list-group-item" @click="startChat(user.id)">
                        {{user.name}}
                    </li>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    props:['user_id'],
    data(){
        return {
            loading:true,
            rooms:[],
            search:'',
            users:[],
            search_users:'',
            openModal:false
        }
    },
    filters: {
        moment: function (date) {
            return moment(date).format('h:mm a');
        }
    },
    methods:{
        loadUsers(){
            axios.get('/all-chat-users',{
                    params: {
                        search:this.search_users
                    }
                }).then(res=>{
                    this.users=res.data.data;
                }).catch(err=>{
                    alert('something went wrong!');
                });
        },
        startChat(id){
            this.openModal=false;
            this.$router.push('/chat/'+id);
        }
    },
    created(){
        localStorage.setItem('auth_id',this.user_id)
        axios.get('/user/chat/rooms').then(res=>{
            let rooms=res.data.data;
            this.rooms=rooms
            this.loading=false
        }).catch(err=>{
            alert('something went wrong!');
        });
        EventBus.$on('chat-open',(user_id)=>{
            let room=this.rooms.find(r=>{
                return r.user_id==user_id
            });
            if(room!=undefined){
                room.unread=0;
            }
        });
        socket.on(`private-rooms-${this.user_id}`, (data) => {
            let room=data.data;
            console.log(room);
            let active_user_id=this.$route.params.id;
                let index=this.rooms.findIndex(u=>{
                    return u.user_id==room.user_id;
                });
                if(index>=0){
                    let new_message_room=this.rooms[index];
                    new_message_room.message=room.message
                    new_message_room.time=room.time
                    if(active_user_id==undefined || (active_user_id!=undefined && active_user_id!=room.user_id)){
                        ++new_message_room.unread;
                        this.rooms.splice(index,1);
                        this.rooms.unshift(new_message_room);
                    }
                }else{
                    room.unread=1
                    this.rooms.unshift(room)
                }
        });
        this.loadUsers();
    }
}
</script>
<style>
    .router-link-active{
        background: #d3d3d3;
    }
    #new-chat{
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        z-index: 9999;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .new-chat-modal{
        width: 400px;
        background: white;
        padding: 20px;
        border-radius: 5px;
    }
    .chat-users-list{
        height: 250px;
        width: 100%;
    }
    .chat-users-list li{
        cursor: pointer;
        color: royalblue;
    }
    .chat-users-list li:hover{
        transition: 0.5s;
        background: #d3d3d3;
    }
</style>
