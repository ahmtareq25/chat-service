<template>
    <div class="card">
        <div class="card-header">
            <div class="media align-items-center" v-if="!loading">
                <img class="mr-3" :src="avatar" alt="Generic placeholder image">
                <div class="media-body">
                    <h5 class="mt-0">{{name}}</h5>
                </div>
            </div>
            <div v-else>
                <div class="shine box-shimmer"></div>
                <div class="parent-shimmer">
                    <div class="shine line-shimmer"></div>
                </div>
            </div>
        </div>
        <div class="card-body">
             <div>
                <div class="scrollbar" id="messages">
                    <infinite-loading direction="top" ref="infiniteScroll" @infinite="infiniteHandler">
                        <div slot="no-more"></div>
                        <div slot="no-results"></div>
                    </infinite-loading>
                    <single-message v-for="message in messages" :key="message.id" :message="message"></single-message>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="send-message">
                    <form id="uploadAttachment" action="" method="post" ref="uploadForm">
                        <button type="button">
                            <img src="/images/attachment.png" alt="upload attachment">
                            <input type="file" ref="file" accept=".pdf,image/*,video/*" @change="uploadImage()">
                        </button>
                    </form>
                    <input type="text" class="text" v-model="message" @keyup.enter="sendMessage">
                </div>
                <div class="send-btn" @click="sendMessage">
                    <img src="/images/send.png" alt="send message">
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import SingleMessage from './SingleMessage.vue'
import InfiniteLoading from 'vue-infinite-loading'
export default {
    components:{SingleMessage,InfiniteLoading},
    props:['id'],
    data(){
        return {
            last_message:0,
            messages:[],
            avatar:'',
            name:'',
            message:'',
            room_id:0,
            last_message_id:0,
            loading:true,
        }
    },
    methods:{
             infiniteHandler($state) {
                axios.get(`/user/chat/rooms/${this.id}/messages`, {
                    params: {
                        message_id: this.last_message_id,
                    },
                }).then(({ data }) => {
                    let messages=data.data.messages;
                    this.avatar=data.data.avatar
                    this.name=data.data.user
                    this.room_id=data.data.room_id
                    this.loading=false
                    if (messages.length) {
                    this.last_message_id= messages[messages.length-1].id;
                    this.messages.unshift(...messages.reverse());
                    $state.loaded();
                    } else {
                    $state.complete();
                    }
                }).catch(err=>{
                    this.$router.push({name:'welcome'});
                });
            },
            scrollToBottom(){
                var objDiv = document.getElementById("messages");
                objDiv.scrollTop = objDiv.scrollHeight;
            },
            sendMessage(){
                if(this.message!=''){
                    let message={
                        'message':this.message
                    };
                    axios.post(`/user/chat/room/${this.id}/message`,message).then(res=>{
                            let message=res.data.data;
                            this.messages.push(message)
                            setTimeout(()=>{
                                this.scrollToBottom();
                            },300);
                        }).catch(err=>{
                            alert('something went wrong!');
                        });
                    this.message=""
                }
            },
            uploadImage(){
                let file=this.$refs.file.files[0];
                let attachment_type='';
                if(file.type.indexOf('image')>=0){
                    attachment_type='image';
                }
                else if(file.type.indexOf('video')>=0){
                    attachment_type='video';
                }
                else if(file.type=='application/pdf'){
                    attachment_type='document';
                }
                if(attachment_type==''){
                    alert('unsupported format')
                }
                let size=file.size/(1024*1024);
                if(size>11){
                    alert('Max upload file size is 10MB.');
                    return;
                }
                let attachment=new FormData();
                attachment.append('attachment',file);
                attachment.append('attachment_type',attachment_type);
                attachment.append('attachment_name',file.name);
                axios.post(`/user/chat/room/${this.id}/message`,attachment).then(res=>{
                        let message=res.data.data;
                        this.messages.push(message)
                        setTimeout(()=>{
                        this.scrollToBottom();
                    },300);
                    }).catch(err=>{
                        alert('something went wrong!');
                    });
                this.$refs.uploadForm.reset();
            }
        },
        created(){
            setTimeout(() => {
                let channel=`private-conversation-${this.id}-${this.room_id}`;
                socket.on(channel, (data) => {
                    let message=data.data;
                    message.send_by_me=false;
                    if(message.sender_id==this.id){
                        this.messages.push(message);
                    }
                    let emit_data={
                        type:'message-seen',
                        message_id:message.id,
                        read_by:localStorage.getItem('auth_id')
                    }
                    socket.emit('emit-data',emit_data);
                    setTimeout(()=>{
                        this.scrollToBottom();
                    },300);
                });
            }, 2000);
            setTimeout(() => {
                EventBus.$emit('chat-open',this.id);
            }, 1000);
        }
    }
</script>
<style lang="scss" scoped>
    .card{
        .card-header{
            padding: 10px;
            img{
                height: 50px;
                width: 50px;
                border-radius: 20px;
            }
        }
    }
    #messages{
        height: 355px;
        overflow-y: auto;
        margin-bottom: 20px;
        padding: 10px;
    }
    .send-message{
        width: 100%;
        position: relative;
        input[type=text]{
            width: 100%;
            border-radius: 30px;
            background: #d3d3d3;
            padding: 10px;
            border: 0px;
            padding-left:50px;
        }
        #uploadAttachment{
            position: absolute;
            top: 50%;
            transform: translate(0px,-50%);
            left: 12px;
            button{
                background: transparent;
                position: relative;
                border: 0px;
                input{
                    position: absolute;
                    height: 100%;
                    width: 100%;
                    left: 0;
                    opacity: 0;
                }
            }
            img{
                height: 25px;
                width: 25px;
            }
        }
    }
    .send-btn{
        height: 46px;
        width: 46px;
        border-radius: 100%;
        background: #d3d3d3;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
        flex-shrink: 0;
        cursor: pointer;
        img{
            height: 20px;
            width: 20px;
        }
    }
</style>
