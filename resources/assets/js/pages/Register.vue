<template>
    <div class="container">
        <section class="main-content">
            <br>
            <div class="row">
                <div class="col-md-push-3 col-md-9 right-aside">
                    <div class="row">
                        <el-card class="box-card">

                            <div slot="header" class="header-text">
                                <span> ثبت نام</span>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <el-form label-position="top" size="small">
                                    <div v-show="registerForm.page == 1">
                                        <el-form-item :class="{'input-empty': errors.has('mobile')}">
                                            <el-input
                                                    suffix-icon="fa fa-phone"
                                                    name="mobile"
                                                    data-vv-as="همراه"
                                                    v-validate="'required|min:10|max:15'"
                                                    v-model="registerForm.data.mobile"
                                                    placeholder="09121234567"
                                            ></el-input>
                                            <span v-show="errors.has('mobile')">{{ errors.first('mobile') }}</span>
                                        </el-form-item>
                                        <el-form-item :class="{'input-empty': errors.has('type') }">
                                            <el-select name="type"
                                                       suffix-icon="fa fa-user"
                                                       data-vv-as="نوع کاربر"
                                                       v-validate="'required'"
                                                       v-model="registerForm.data.type"
                                                       placeholder="نوع کاربر"
                                            >
                                                <el-option label="تولید کننده" value="brand"></el-option>
                                                <el-option label="مصرف کننده" value="p_customer"></el-option>
                                                <el-option label="عمده فروش (عرضه کننده)"
                                                           value="s_customer"></el-option>
                                            </el-select>
                                            <span v-show="errors.has('type')">{{ errors.first('type') }}</span>
                                        </el-form-item>
                                        <el-form-item :class="{'input-empty': errors.has('password')}">
                                            <el-input name="password"
                                                      suffix-icon="fa fa-lock"
                                                      data-vv-as="رمز عبور"
                                                      v-validate="'required|min:6'"
                                                      v-model="registerForm.data.password"
                                                      type="password"
                                                      placeholder="رمز عبور"
                                            ></el-input>
                                            <span v-show="errors.has('password')">{{ errors.first('password') }}</span>
                                        </el-form-item>
                                        <el-alert
                                                class="login-errors"
                                                v-for="(error,key) in registerForm.errors"
                                                :title="error[0]"
                                                :key="key"
                                                type="error"
                                                :closable="false"
                                        >
                                        </el-alert>
                                        <br>
                                        <el-form-item>
                                            <div class="form-buttons">
                                                <el-button v-loading.fullscreen.lock="fullscreenLoading"
                                                           @click="register()" size="small" round type="success">ثبت نام
                                                    <i class="el-icon-arrow-left el-icon-right"></i></el-button>
                                            </div>
                                        </el-form-item>
                                    </div>
                                    <div v-show="registerForm.page == 2">
                                        <el-form-item :class="{'input-empty': errors.has('verify')}">
                                            <h5 style="text-align:center;">کد 5 رقمی به شماره شما ارسال شده.آن را در
                                                کادر زیر وارد کنید.</h5>
                                            <el-input
                                                    suffix-icon="fa fa-check"
                                                    name="verify"
                                                    data-vv-as="کد تایید"
                                                    v-validate="'required|digits:5'"
                                                    v-model="registerForm.data.verify"
                                                    placeholder="کد تایید"
                                            ></el-input>
                                            <span v-show="errors.has('verify')">{{ errors.first('verify') }}</span>
                                        </el-form-item>
                                        <el-alert
                                                class="login-errors"
                                                v-for="(error,key) in registerForm.errors"
                                                :title="error[0]"
                                                :key="key"
                                                type="error"
                                                :closable="false"
                                        >
                                        </el-alert>

                                        <br>
                                        <el-form-item>
                                            <div class="form-buttons">
                                                <el-button v-loading.fullscreen.lock="fullscreenLoading"
                                                           @click="verify()" size="small" round type="success">تایید
                                                    <i class="el-icon-arrow-left el-icon-right"></i></el-button>
                                            </div>
                                        </el-form-item>
                                    </div>

                                </el-form>
                            </div>
                        </el-card>

                    </div>
                </div>
                <div class="col-md-pull-9 col-md-3">
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
            if (this.auth) {
                this.$router.go(-1);
            }
            this.getProvinces();
            this.setCartProducts();
        },
        data(){
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                cartProducts: [],
                numberCartProducts: [],
                idCartProducts: [],
                registerForm: {
                    page: 1,
                    mode: '',
                    errors: [],
                    loading: false,
                    data: {
                        type: 'brand',
                        mobile: '',
                        password: '',
                        verify: '',
                    }
                },
                provinces: [],
                cities: [],

            }
        },
        methods: {
            updateNumberProduct(numbers){
                this.numberCartProducts = numbers;
                this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
            },
            getProvinces(){
                var vm = this;
                axios.get('/v1/users/get-provinces').then(function (response) {
                    vm.provinces = response.data;
                })
            },
            getCities(){
                var vm = this;
                axios.get(`/v1/users/get-cities/${vm.registerForm.data.province}`).then(function (response) {
                    vm.cities = response.data;
                })
            },
            totalPrice(){
                var sum = 0;

                for (var i = 0; i < this.cartProducts.length; i++) {
                    sum += this.cartProducts[i].price * this.numberCartProducts[i];
                }
                return sum;
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

                            vm.registerForm.errors = error.response.data.errors;
                            vm.fullscreenLoading = false;

                        });
                    }
                });

            },
            verify(){
                var vm = this;
                var cartProduct = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                this.$validator.validateAll({verify: vm.registerForm.data.verify}).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;
                        axios.post('/v1/verify', vm.registerForm.data).then(function (response) {
                            if (response.status == 200) {
                                window.Laravel.auth = response.data.auth;
                                if (cartProduct.length > 0)
                                    window.location.href = '/checkout'
                                else
                                    window.location.href = '/dashboard'
//                                vm.$router.push({
//                                    path: '/order'
//                                });
                                vm.fullscreenLoading = false;
                                vm.$message({
                                    message: response.data.data,
                                    type: 'success',
                                    duration: 5000
                                });
                            }
                        }).catch(function (error) {
                            vm.fullscreenLoading = false;
                            vm.registerForm.errors = error.response.data.errors;
                        });
                    }
                });

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
        }
    }
</script>

<style>
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

    .col-md-6 {
        margin-top: 15px;
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