<template>
    <ul class="nav navbar-nav dashboard-menu">
        <li :class="{acitve: activeMenu === 'dashboard'}">
            <router-link :to="{name: 'dashboard.orders'}">
                <span class="glyphicon glyphicon-home"></span> داشبورد
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1" :class="{acitve: activeMenu === 'users'}">
            <router-link :to="{name: 'users'}">
                <span class="glyphicon glyphicon-user"></span> کاربران
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1 || checkRole('brand') != -1" :class="{acitve: activeMenu === 'products'}">
            <router-link :to="{name: 'products'}">
                <span class="fa fa-medkit"></span> محصولات
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1" :class="{acitve: activeMenu === 'packages'}">
            <router-link :to="{name: 'packages'}">
                <span class="fa fa-archive"></span> پکیج ها
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1" :class="{acitve: activeMenu === 'categories'}">
            <router-link :to="{name: 'categories'}">
                <span class="fa fa-tasks"></span> دسته بندی ها
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1"  :class="{acitve: activeMenu === 'orders'}">
            <router-link :to="{name: 'orders'}">
                <span class="fa fa-shopping-cart"></span> سفارشات
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1"  :class="{acitve: activeMenu === 'brands'}">
            <router-link :to="{name: 'brands'}">
                <span class="fa fa-apple"></span> برند ها
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1"  :class="{acitve: activeMenu === 'pages'}">
            <router-link :to="{name: 'pages'}">
                <span class="fa fa-file-text-o"></span> صفحه ها
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1"  :class="{acitve: activeMenu === 'informations'}">
            <router-link :to="{name: 'informations'}">
                <span class="fa fa-paperclip"></span> اطلاعیه ها
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1"  :class="{acitve: activeMenu === 'carousels'}">
            <router-link :to="{name: 'carousels'}">
                <span class="fa fa-file-image-o"></span> اسلایدر ها
            </router-link>
        </li>
        <li v-if="checkRole('admin') != -1"  :class="{acitve: activeMenu === 'settings'}">
            <router-link :to="{name: 'settings'}">
                <span class="fa fa-gears"></span> تنظیمات
            </router-link>
        </li>
    </ul>

</template>

<script>
    export default {
        data() {
            return {
                user: window.Laravel.user,
                activeMenu: ''
            }
        },mounted(){
            this.activeMenu = this.$route.name.split('.')[0];
        },
        watch: {
            '$route' (to, from) {
                this.activeMenu = to.name.split('.')[0];
            }
        },
        methods:{
            checkRole(role){
                return this.user.role_user.indexOf(role);
            }
        }
    }
</script>

<style scoped>
    .dashboard-menu li {
        opacity: 0.7;
        width: auto;
    }
    .dashboard-menu li a{
        font-size:12px;
    }

    .dashboard-menu li.acitve {
        font-weight: bold;
        opacity: 1;
    }
    .dashboard-menu li.acitve a{
        font-size:13px;
        color: dodgerblue !important;

    }

</style>