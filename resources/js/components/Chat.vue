<template>
    <div class="row no-gutters">
        <div class="col-md-3">
            <rooms :user_id="user_id"></rooms>
        </div>
        <div class="col-md-9">
            <router-view :key="$route.path" ></router-view>
        </div>
    </div>
</template>
<script>

import Rooms from './Rooms.vue'
export default {
    props:['user_id'],
    components:{
        Rooms
    }
}
</script>
