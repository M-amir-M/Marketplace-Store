<template>
    <div class="row">
        <div class="col-md-9">
            <router-view></router-view>
        </div>
        <div class="col-md-3">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span class="text-center">
                        <el-rate v-if="user.buy_amount"
                                 v-model="user.buy_amount['star']"
                                 disabled
                                 text-color="#ff9900">
                        </el-rate>
                    </span>
                </div>
                <router-link v-if="checkRole('admin') == -1" :to="{name: 'dashboard.orders'}">
                    <div class="text item" :class="activeSection == 'dashboard.orders'?'active':''">آرشیو سفارشات</div>
                </router-link>
                <router-link :to="{name: 'dashboard.profile'}">
                    <div class="text item" :class="activeSection == 'dashboard.profile'?'active':''">اطلاعات شخصی</div>
                </router-link>
                <router-link :to="{name: 'dashboard.password'}">
                    <div class="text item" :class="activeSection == 'dashboard.password'?'active':''">تغیر رمز عبور
                    </div>
                </router-link>
                <router-link :to="{name: 'dashboard.favorites'}">
                    <div class="text item" :class="activeSection == 'dashboard.favorites'?'active':''">علاقه مندی ها
                    </div>
                </router-link>
            </el-card>

        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                activeSection: '',
                user: {}
            }
        },
        computed: {
            star(){

            }
        },
        mounted(){
            if (!this.auth) {
                this.$router.go(-1);
            }
            this.activeSection = this.$route.name;
            this.getUser();
        },
        watch: {
            '$route' (to, from) {
                this.activeSection = to.name;
            }
        },
        methods: {
            getUser(){
                var vm = this;
                axios.get('/v1/users/get-auth').then(function (response) {
                    if (response.status == 200) {
                        vm.user = response.data;
                    }
                }).catch(function (error) {

                })
            },
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
        }
    }
</script>
<style scoped>
    .active {
        background-color: #EEEEEE;
        border-radius: 2px;
    }

    a {
        text-decoration: none;
    }

    .item {
        padding: 10px;
    }

    .item:hover {
        color: red;
        padding-right: 15px;
        transition: all, .3s, ease-in;
        -webkit-transition: all, .3s, ease-in;
        -moz-transition: all, .3s, ease-in;
        -ms-transition: all, .3s, ease-in;
    }
</style>