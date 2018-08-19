require('./bootstrap');
window.Vue = require('vue');
var VueCookie = require('vue-cookie');
var moment = require('moment-jalaali');
moment.loadPersian({usePersianDigits: true});
//VeeValidate
import VeeValidate from 'vee-validate';
//import for locale
import {Validator} from 'vee-validate';
import farsi from 'vee-validate/dist/locale/fa';

//config for locale
Vue.use(VeeValidate, {
    errorBagName: 'errors', // change if property conflicts
    fieldsBagName: 'fields',
    delay: 0,
    locale: 'fa',
    dictionary: null,
    strict: true,
    classes: false,
    classNames: {
        touched: 'touched', // the control has been blurred
        untouched: 'untouched', // the control hasn't been blurred
        valid: 'is-success', // model is valid
        invalid: 'is-danger', // model is invalid
        pristine: 'pristine', // control has not been interacted with
        dirty: 'dirty' // control has been interacted with
    },
    events: 'input|blur',
    inject: true,
    validity: false,
    aria: true
});
//localize fa
Validator.localize('fa', farsi);


import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css';


Vue.component('cart', require('./components/Cart.vue'));
Vue.component('products', require('./components/Products.vue'));
Vue.component('product', require('./components/Product.vue'));
Vue.component('top-menu', require('./components/Menu.vue'));
Vue.component('mega-menu', require('./components/MegaMenu.vue'));


Vue.use(VueRouter);
Vue.use(ElementUI);
Vue.use(VueCookie);


const routes = [
    {
        path: '/',
        name: 'home',
        component: require('./pages/Home.vue')
    },
    {
        path: '/dashboard',
        component: require('./pages/Dashboard.vue'),
        children: [
            {
                path: '',
                name:'dashboard.orders',
                component: require('./components/DetailOrders.vue')
            },
            {
                path: 'profile',
                name:'dashboard.profile',
                component: require('./components/Profile.vue')
            },
            {
                path: 'password',
                name:'dashboard.password',
                component: require('./components/ChangePassword.vue')
            },
            {
                path: 'favorites',
                name:'dashboard.favorites',
                component: require('./components/Favorite.vue')
            }
        ]
    },
    {
        path: '/dashboard/users',
        name: 'users',
        component: require('./pages/Users.vue')
    },
    {
        path: '/dashboard/products',
        name: 'products',
        component: require('./pages/Products.vue')
    },
    {
        path: '/dashboard/packages',
        name: 'packages',
        component: require('./pages/Packages.vue')
    },
    {
        path: '/dashboard/package/:id',
        name: 'package',
        component: require('./pages/Package.vue')
    },
    {
        path: '/dashboard/orders',
        name: 'orders',
        component: require('./pages/Orders.vue')
    },
    {
        path: '/dashboard/orders/:id',
        name: 'user-orders',
        component: require('./pages/Orders.vue')
    },
    {
        path: '/dashboard/categories',
        name: 'categories',
        component: require('./pages/Categories.vue')
    },
    {
        path: '/product/:type/:id/:title',
        name: 'product.detail',
        component: require('./pages/Product.vue')
    },
    {
        path: '/dashboard/brands',
        name: 'brands',
        component: require('./pages/Brands.vue')
    },
    {
        path: '/dashboard/pages',
        name:'pages',
        component: require('./pages/Pages.vue'),
    },
    {
        path: '/dashboard/informations',
        name:'informations',
        component: require('./pages/Informations.vue'),
    },
    {
        path: '/dashboard/carousels',
        name:'carousels',
        component: require('./pages/Carousel.vue'),
    },
    {
        path: '/dashboard/settings',
        name:'settings',
        component: require('./pages/Settings.vue')
    },
    {
        path: '/page/:id',
        name:'pages.show',
        component: require('./pages/Page.vue'),
    },
    {
        path: '/search/:term/page/:page',
        name:'filter.search',
        component: require('./pages/Filter.vue')
    },
    {
        path: '/category/:category_name/:category_id/page/:page',
        name:'filter.category',
        component: require('./pages/Filter.vue')
    },
    {
        path: '/brand/:brand_name/:brand_id/page/:page',
        name:'filter.brand',
        component: require('./pages/Filter.vue')
    },
    {
        path: '/checkout',
        name:'checkout',
        component: require('./pages/Checkout.vue'),

    },
    {
        path: '/login',
        name:'pages.login',
        component: require('./pages/Login.vue'),

    },
    {
        path: '/register',
        name:'pages.register',
        component: require('./pages/Register.vue'),
    },
    // {
    //     path:'*',
    //     redirect:{ name: 'home' }
    // }
]

const router = new VueRouter({
    mode:'history',
    routes: routes
})

const app = new Vue({
    router: router,
    el: '#app',
    data(){
        return {
            auth: window.Laravel.auth,
            cartNumber: '0',
            health_dialog:false,
            fullscreenLoading:false,
            jdate: '',
            provinces: [],
            cities: [],
            loginForm: {
                visible: false,
                errors:[],
                data: {
                    mobile: '',
                    password: '',
                    _token: '',
                    remember: ''
                }
            },
            filters:{
                search: '',
                category: '',
            },
            settings:[],
            registerForm: {
                page: 1,
                mode: '',
                visible: false,
                title: '',
                errors: [],
                loading: false,
                data: {
                    type: 'brand',
                    mobile: '',
                    password: '',
                    verify: '',
                }
            },
            orderForm: {
                page: 1,
                data: {
                    name: '',
                    type: 'brand',
                    company: '',
                    email: '',
                    phone: '',
                    mobile: '',
                    password: '',
                    province: '',
                    city: '',
                    local: '',
                    address: '',
                }
            },
            orderInfo: {
                name: '',
                payment: 'at_place',
                address_id: '',
                description: '',
                numberCartProducts: [],
                idCartProducts: [],
            },
            user: [],
            products: [],
            cartProducts: [],
            address: {
                visible: false,
                operation: 'add',
                title: '',
                loading: false,
                data: {
                    id: '',
                    province: '',
                    city: '',
                    local: '',
                    address: '',
                }
            },
        }
    },
    computed:{
        isOrderComplete(){
            let info = this.orderInfo;
            if(info.name != '' && info.address_id != '' && info.numberCartProducts.length>0&& info.idCartProducts.length>0)
                return true;
            return false;
        }
    },
    mounted () {
        var cartProducts = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
        this.cartNumber = cartProducts.length;
        this.getSettings();
        this.getUser();
        this.updateDateTime();
        setInterval(this.updateDateTime, 60000);
    },
    methods: {
        getSettings(){
            var vm = this;
            vm.loading = true;
            axios.get(`/v1/settings/get-settings`).then(function (response) {
                vm.settings = response.data;
                vm.health_dialog= vm.settings['health-dialog-status'].value == 0? false:true;
                vm.loading = false;
            })
        },
        searchProduct(){
            router.push({path: `/search/${this.filters.search}/page/1`, params: {term: this.filters.search}});
        },
        register(){
            var vm = this;
            var form = vm.registerForm.data;
            var form2 = {
                type: form.type,
                mobile: form.mobile,
                password: form.password,
            }

            this.$validator.validateAll(form2).then((result) => {
                if (result) {
                    vm.fullscreenLoading = true;
                    axios.post('/register', vm.registerForm.data).then(function (response) {
                        if (response.status == 200) {
                            vm.registerForm.page = 2;
                            vm.fullscreenLoading = false;
                        }
                    }).catch(function (error) {
                        vm.fullscreenLoading = false;
                        vm.registerForm.errors = error.response.data.errors;
                    });
                }
            });

        },
        verify(){
            var vm = this;
            this.$validator.validateAll({verify: vm.registerForm.data.verify}).then((result) => {
                if (result) {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/verify', vm.registerForm.data).then(function (response) {
                        if (response.status == 200) {
                            vm.$message({
                                message: response.data.data,
                                type: 'success',
                                duration:5000
                            });
                            vm.registerForm.visible = false;
                            vm.fullscreenLoading = false;
                        }
                    }).catch(function (error) {
                        vm.registerForm.errors = error.response.data.errors;
                        vm.fullscreenLoading = false;
                    });
                }
            });

        },
        getUser(){
            var vm = this;
            axios.get('/v1/users/get-auth').then(function (response) {
                if (response.status == 200) {
                    vm.user = response.data;
                    vm.orderInfo.name = vm.user.name;
                    vm.orderInfo.address_id = vm.user.address_id;
                }
            })
        },
        login(){
            var vm = this;
            let token = document.head.querySelector('meta[name="csrf-token"]').content;
            if (token) {
                vm.loginForm.data._token = token;
                var form = vm.loginForm.data;
                var form2 = {
                    mobile: form.mobile,
                    password: form.password,
                }

                this.$validator.validateAll(form2).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;
                        axios.post('/login', vm.loginForm.data).then(function (response) {
                            if (response.status == 200) {
                                window.Laravel.auth = response.data.auth;
                                vm.auth = response.data.auth;
                                vm.$router.push({
                                    path: '/order'
                                });
                                vm.loginForm.visible = false;
                                vm.fullscreenLoading = false;
                            }
                        }).catch(function (error) {
                            vm.loginForm.errors = error.response.data.errors;
                            vm.fullscreenLoading = false;
                        });
                    }
                });
            }

        },
        getProvinces(){
            var vm = this;
            axios.get('/v1/users/get-provinces').then(function (response) {
                vm.provinces = response.data;
            })
        },
        getCities(){
            var vm = this;
            axios.get(`/v1/users/get-cities/${vm.address.data.province}`).then(function (response) {
                vm.cities = response.data;
            })
        },
        updateDateTime () {
            this.jdate = moment().format(' dddd jDD jMMMM jYYYY ساعت HH:mm');
        },
        paymentMsg() {
            Message({
                message: document.getElementById('paymentMessageInput').value,
                type: document.getElementById('paymentStatusInput').value,
                duration: 7000
            });
        },

    }

});
