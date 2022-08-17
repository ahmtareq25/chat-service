<template>
    <div :class="message.send_by_me?'user1':'user2'">
        <div class="message">
            <div v-if="message.attachment!=null">
                <div class="video-msg" v-if="message.attachment.type=='video'">
                    <img src="/images/video.jpeg" :alt="message.attachment.name">
                    <div class="play-video-icon">
                        <i class="fas fa-play" @click="showAttachment()"></i>
                    </div>
                </div>
                <div v-else-if="message.attachment.type=='document'">
                    <a class="document" :href="message.attachment.attachment" target="_blank">
                        <img src="/images/pdf.png" alt="pdf">
                        <p class="document-name text-break">
                            {{message.attachment.name}}
                        </p>
                    </a>
                </div>
                <div v-else>
                    <img :src="message.attachment.attachment" :alt="message.attachment.name" @click="showAttachment()">
                </div>
            </div>
            {{message.message}}
            <small class="float-right time">
                {{message.created_at | moment}}
            </small>
        </div>
        <light-box v-if="message.attachment!=null && message.attachment.type!='document'"
            ref="lightbox"
            :media="attachment"
            :show-light-box="false"
            :show-thumbs="false"
        >
        </light-box>
        </div>
    <!-- </div> -->
</template>
<script>
import LightBox from 'vue-it-bigger'
import('vue-it-bigger/dist/vue-it-bigger.min.css')
export default {
    components:{
        LightBox
    },
    props:['message'],
    computed:{
        attachment(){
            let type=this.message.attachment.type;
            let src=this.message.attachment.attachment;
            let attachment={};
            attachment.type=type
            if(type=='image'){
                attachment.src=src;
            }
            else if(type=='video'){
                attachment.sources=[{
                    src:src,
                    type:'video/mp4'
                }]
                attachment.autoplay=false
                attachment.width=500
                attachment.height=500
            }
            return [attachment];
        }
    },
    methods:{
        showAttachment(){
            this.$refs.lightbox.showImage(0);
        }
    },
    filters: {
        moment: function (date) {
            return moment(date).format('h:mm a');
        }
    }
}
</script>
<style scoped lang="scss">
    .message{
        display: inline-block;
        padding: 8px;
        border-radius: 5px;
        box-shadow: 0px 0px 5px #d3d3d3;
        margin-bottom: 15px;
    }
    .user1{
        display: flex;
        justify-content: flex-end;
    }
    .user2 .message{
        background: #d3d3d3;
    }
    .user2{
        display: flex;
        justify-content: flex-start;
    }
    .time{
        margin:0px 5px;
        position: relative;
        bottom: -4px;
        font-size: 10px;
    }
    .message img{
        height: 150px;
        width: 250px;
        cursor: pointer;
        object-fit: cover;
        margin-bottom: 5px;
    }
    .video-msg{
        position: relative;
        margin-bottom: 5px;
        img{
            margin: 0px;
        }
        .play-video-icon{
            position: absolute;
            height: 100%;
            width: 100%;
            top: 0;
            left: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            i{
                font-size: 40px;
                color: white;
                cursor: pointer;
            }
        }
    }
    .document{
        display: flex;
        background: rgba(0,0,0,0.1);
        border-radius: 5px;
        padding: 5px;
        width: 200px;
        p{
            margin:0px;
            // margin-top: 10px;
            color: black;
            font-size: 13px;
        }
        img{
            height: 40px;
            width: 40px;
            flex-shrink: 0;
            flex-grow: 0;
            margin-right: 5px;
        }
    }
</style>
