<template>
    <div class="container">
        <section class="main-content">
            <br>
            <div class="row">
                <div class="col-md-push-3 col-sm-9 right-aside">
                    <div class="row">

                        <el-card  v-if="typeForm == 'login'"  class="box-card">
                            <div slot="header" class="header-text">
                                <span> ورود</span>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-6">

                                <h5 class="register-link">اگر عضو نیستید همین حالا
                                    <router-link :to="{name : 'pages.register'}">ثبت نام</router-link>
                                    کنید.
                                </h5>
                                <el-form label-position="top" size="small"
                                >
                                    <el-form-item :class="{'input-empty': errors.has('mobile')}">
                                        <el-input
                                                suffix-icon="fa fa-phone"
                                                name="mobile"
                                                data-vv-as="همراه"
                                                v-validate="'required|min:10|max:15'"
                                                v-model="loginForm.data.mobile"
                                                placeholder="09121234567"
                                        ></el-input>
                                        <span v-show="errors.has('mobile')">{{ errors.first('mobile') }}</span>
                                    </el-form-item>
                                    <el-form-item :class="{'input-empty': errors.has('password')}">
                                        <el-input name="password"
                                                  suffix-icon="fa fa-lock"
                                                  data-vv-as="رمز عبور"
                                                  v-validate="'required|min:6'"
                                                  v-model="loginForm.data.password"
                                                  type="password"
                                                  placeholder="رمز عبور"
                                        ></el-input>
                                        <span v-show="errors.has('password')">{{ errors.first('password') }}</span>
                                    </el-form-item>
                                    <el-form-item>
                                        <a href="#" @click.prevent="typeForm = 'forgot'">فراموشی رمز عبور</a>
                                    </el-form-item>
                                    <el-alert
                                            class="login-errors"
                                            v-for="(error,key) in loginForm.errors"
                                            :title="error[0]"
                                            :key="key"
                                            type="error"
                                            :closable="false"
                                    >
                                    </el-alert>

                                    <br>
                                    <el-form-item>
                                        <div class="form-buttons">
                                            <el-button @click="login()" size="small" round type="success" v-loading.fullscreen.lock="fullscreenLoading">ورود
                                                <i class="el-icon-arrow-left el-icon-right"></i></el-button>
                                        </div>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </el-card>

                        <el-card v-if="typeForm == 'forgot'" class="box-card">
                            <div slot="header" class="header-text">
                                <span> فراموشی رمز عبور</span>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-6">

                                <el-form label-position="top" size="small">
                                    <el-form-item :class="{'input-empty': errors.has('mobile')}">
                                        <el-input
                                                suffix-icon="fa fa-phone"
                                                name="mobile"
                                                data-vv-as="همراه"
                                                v-validate="'required|min:10|max:15'"
                                                v-model="loginForm.data.mobile"
                                                placeholder="09121234567"
                                        ></el-input>
                                        <span v-show="errors.has('mobile')">{{ errors.first('mobile') }}</span>
                                    </el-form-item>
                                    <el-alert
                                            class="login-errors"
                                            v-for="(error,key) in loginForm.errors"
                                            :title="error[0]"
                                            :key="key"
                                            type="error"
                                            :closable="false"
                                    >
                                    </el-alert>

                                    <br>
                                    <el-form-item>
                                        <div class="form-buttons">
                                            <el-button @click="resetPass()" size="small" round type="success" v-loading.fullscreen.lock="fullscreenLoading">ارسال کد
                                                <i class="el-icon-arrow-left el-icon-right"></i></el-button>
                                        </div>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </el-card>

                        <el-card  v-if="typeForm == 'verify'"  class="box-card">
                            <div slot="header" class="header-text">
                                <span>بازیابی رمز عبور</span>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <br>
                                <el-form label-position="top" size="small">

                                    <el-form-item :class="{'input-empty': errors.has('new') }" label="رمز عبور جدید :">
                                        <el-input
                                                placeholder="رمز عبور جدید"
                                                suffix-icon="fa fa-lock"
                                                v-model="forgot.data.new"
                                                v-validate="'required|min:5'"
                                                name="new"
                                                type="password"
                                                data-vv-as="رمز عبور جدید">
                                        </el-input>
                                        <span v-show="errors.has('new')">{{ errors.first('new') }}</span>
                                    </el-form-item>

                                    <el-form-item :class="{'input-empty': errors.has('confirm') }" label="تکرار رمز عبور جدید :">
                                        <el-input
                                                placeholder="تکرار رمز عبور جدید"
                                                suffix-icon="fa fa-lock"
                                                v-model="forgot.data.confirm"
                                                v-validate="'confirmed:new'"
                                                name="confirm"
                                                type="password"
                                                data-vv-as="تکرار رمز عبور جدید">
                                        </el-input>
                                        <span v-show="errors.has('confirm')">{{ errors.first('confirm') }}</span>
                                    </el-form-item>

                                    <el-form-item :class="{'input-empty': errors.has('verify')}">
                                        <h5 style="text-align:right;">کد 5 رقمی به شماره شما ارسال شده.آن را در کادر زیر وارد کنید.</h5>
                                        <el-input
                                                suffix-icon="fa fa-check"
                                                name="verify"
                                                data-vv-as="کد تایید"
                                                v-validate="'required|digits:5'"
                                                v-model="forgot.data.verify"
                                                placeholder="کد تایید"
                                        ></el-input>
                                        <span v-show="errors.has('verify')">{{ errors.first('verify') }}</span>
                                    </el-form-item>
                                    <input type="hidden"
                                           name="mobile"
                                           v-validate="'required'"
                                           v-model="loginForm.data.mobile">
                                    <br>
                                    <el-form-item>
                                        <div class="form-buttons">
                                            <el-button @click="changePassword()" size="small" round type="success">بروزرسانی رمز عبور</el-button>
                                        </div>
                                    </el-form-item>
                                </el-form>
                            </div>
                        </el-card>

                    </div>
                </div>
                <div class="col-md-pull-9 col-sm-3">
                    <cart :number-products="numberCartProducts" @update-number="updateNumberProduct($event)"
                          :remove-cart="removeCart" :products="cartProducts"></cart>
                </div>
            </div>
            <br>
        </section>
    </div>
</template>

<script>
    export default{
        mounted(){
            if(this.auth){
                this.$router.go(-1);
            }
            this.setCartProducts();
        },
        data(){
            return {
                auth: window.Laravel.auth,
                typeForm:'login',
                loginForm: {
                    visible: false,
                    errors: [],
                    data: {
                        mobile: '',
                        password: '',
                        _token: '',
                        remember: ''
                    }
                },
                forgot: {
                    data: {
                        verify: '',
                        mobile: '',
                        new: '',
                        confirm: '',
                    }
                },
                fullscreenLoading:false,
                cartProducts: [],
                numberCartProducts: [],
                idCartProducts: [],

            }
        },
        methods: {
            resetPass(){
                var vm = this;
                var form = vm.loginForm.data;
                var form2 = {
                    mobile: form.mobile,
                }
                this.$validator.validateAll(form2).then((result) => {
                    if (result) {
                        this.fullscreenLoading = true;
                        axios.post('/v1/users/reset-password', {mobile:form2.mobile}).then(function (response) {
                            if (response.status == 200) {
                                vm.fullscreenLoading = false;
                                vm.typeForm = 'verify'
                            }
                        }).catch(function (error) {
                            vm.loginForm.errors = error.response.data.errors;
                            vm.fullscreenLoading = false;
                        });
                    }
                });

            },
            changePassword(){
                var vm = this;
                var form = vm.forgot.data;
                form.mobile = vm.loginForm.data.mobile;
                this.$validator.validateAll(form).then((result) => {
                    if (result) {
                        this.fullscreenLoading = true;
                        axios.post('/v1/users/reset-password/verify', vm.forgot.data).then(function (response) {
                            if (response.status == 200) {
                                vm.$notify({
                                    title: ' بروزرسانی',
                                    message: 'بروزرسانی رمز عبور با موفقیت انجام شد.',
                                    type: 'success',
                                });
                                vm.fullscreenLoading = false;
                                vm.$router.push({
                                    path: '/order'
                                });
                            }
                        }).catch(function (error) {
                            vm.loginForm.errors = error.response.data.errors;
                            vm.fullscreenLoading = false;
                        });
                    }
                });
            },
            updateNumberProduct(numbers){
                this.numberCartProducts = numbers;
                this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
            },
            totalPrice(){
                var sum = 0;

                for (var i = 0; i < this.cartProducts.length; i++) {
                    sum += this.cartProducts[i].price * this.numberCartProducts[i];
                }
                return sum;
            },
            addCart(product, type){
                var ids = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                var numbers = this.$cookie.get('numberProducts') == null ? [] : JSON.parse(this.$cookie.get('numberProducts'));
                var status = false, indexNumber = '';
                for (var index in types) {
                    if (types[index] == type && ids[index] == product.id) {
                        status = true
                        indexNumber = index;
                        break;
                    }

                }
                if (!status) {
                    ids.push(product.id);
                    types.push(type);
                    numbers.push(1);
                    this.$cookie.set('cart', JSON.stringify(ids), 1);
                    this.$cookie.set('types', JSON.stringify(types), 1);
                    this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
                    this.setCartProducts();
                    this.$parent.cartNumber = numbers.length;
                } else {
                    numbers[indexNumber] += 1;
                    this.numberCartProducts = numbers;
                    this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
                }
                $('.add-to-cart').on('click', function () {
                    //Scroll to top if cart icon is hidden on top
                    $('html, body').animate({
                        'scrollTop': $(".cart_anchor").position().top + 300
                    });
                    //Select item image and pass to the function
                    var itemImg = $(this).parent().parent().find('img').eq(0);
                    flyToElement($(itemImg), $('.cart_anchor'));
                });
            },
            removeCart(index){
                var product = this.cartProducts[index];
                var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                var ids = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var type = types[index];

                if (ids.indexOf(product.id) != -1 && types.indexOf(type) != -1) {
                    var idIndex = ids.indexOf(product.id);
                    ids.splice(idIndex, 1);
                    types.splice(idIndex, 1);
                    this.cartProducts.splice(index, 1);
                    this.numberCartProducts.splice(index, 1);
                    this.$cookie.set('numberProducts', JSON.stringify(this.numberCartProducts), 1);
                    this.$cookie.set('cart', JSON.stringify(ids), 1);
                    this.$cookie.set('types', JSON.stringify(types), 1);
                    this.$parent.cartNumber = this.numberCartProducts.length;
                }
            },
            setCartProducts(){
                var vm = this;
                this.idCartProducts = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                this.numberCartProducts = this.$cookie.get('numberProducts') == null ? [] : JSON.parse(this.$cookie.get('numberProducts'));
                axios.get(`/v1/products/get-cart-products/${JSON.stringify({ids:this.idCartProducts,types:types})}`).then(function (response) {
                    vm.cartProducts = response.data;
                })
            },
            login(){
                var vm = this;
                var cartProduct = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var form = vm.loginForm.data;
                var form2 = {
                    mobile: form.mobile,
                    password: form.password,
                }

                this.$validator.validateAll(form2).then((result) => {
                    if (result) {
                        this.fullscreenLoading = true;
                        axios.post('/login', vm.loginForm.data).then(function (response) {
                            if (response.status == 200) {
                                window.Laravel.auth = response.data.auth;
                                if (vm.idCartProducts.length > 0)
                                    window.location.href = '/checkout'
                                else
                                    window.location.href = '/dashboard'
                                vm.fullscreenLoading = false;
                            }
                        }).catch(function (error) {
                            vm.loginForm.errors = error.response.data.errors;
                            vm.fullscreenLoading = false;
                        });
                    }
                });

            },

        }
    }
</script>

<style>
    .login-errors{
        margin-bottom: 2px;
    }
    .right-aside {
        margin-left: 15px;
    }

    .main-content > .row {
        display: flex;
        flex-direction: row-reverse;
    }

    .main-content {
        margin: 0 15px;
    }

    .header-text {
        text-align: center;
    }

    .register-link {
        text-align: center;
        margin: 20px 0;
    }

    @media (max-width: 768px) {
        .right-aside {
            margin-left: 0px;
            margin-bottom: 10px;
        }

        .main-content > .row {
            display: block;
        }
    }
</style>