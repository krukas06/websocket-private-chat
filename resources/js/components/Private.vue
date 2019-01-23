<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <textarea class="form-control" rows="10" readonly="">{{messages.join('\n')}}</textarea>
                <hr>
                <input type="text" class="form-control" v-model="textMessage" @keyup.enter="sendMessage" @keydown="ActionUser">
                <span v-if="isActive">{{isActive.name}} набирает сообщение...</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                messages:[],
                textMessage: '',
                isActive: false,
                typingTimer: false
            }
        },

        props: ['room', 'user'],

        computed: {

            channel(){
                return window.Echo.private('room.' + this.room.id);
            }
        },

        mounted() {
            this.channel
                .listen('PrivateChat', ({data})=>{
                    this.messages.push(data.body);
                    this.isActive = false;
                })
                .listenForWhisper('typing', (e)=>{
                    this.isActive = e;

                    if(this.typingTimer) clearTimeout(this.typingTimer);

                    this.typingTimer = setTimeout(()=>{
                        this.isActive = false;
                    }, 2000);
                });
        },

        methods: {
            sendMessage(){
                axios.post('/messages',{body: this.textMessage, room_id: this.room.id});

                this.messages.push(this.textMessage);
                this.textMessage = '';
            },

            ActionUser(){
                this.channel.
                    whisper('typing', {
                    name:this.user.name
                });
            }
        }
    }
</script>
