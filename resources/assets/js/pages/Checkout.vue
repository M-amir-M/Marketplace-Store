<template>
    <div class="container">
        <section id="ordercart">
            <div class="row">
                <el-card class="box-card">
                    <div slot="header">
                        <div>
                            <span class=" icon fa fa-shopping-cart"></span>&nbsp;&nbsp;
                            <span>اطلاعات سفارشات</span>
                        </div>
                    </div>

                    <div class="row">
                        <h4 class="heading-order-part">اطلاعات گیرنده سفارش</h4>
                        <br>
                        <dl class="dl-horizontal">
                            <dt>نام :</dt>
                            <dd v-if="user.name != null" v-text="user.name"></dd>
                            <dd v-if="user.name == null">
                                <el-form :inline="true" class="demo-form-inline" size="mini">
                                    <el-form-item :class="{'input-empty': errors.has('name') }" label="">
                                        <el-input name="name" data-vv-as="نام و نام خانوادگی"
                                                  v-validate="'required|min:5'" v-model="orderInfo.name"
                                                  placeholder="نام و نام خانوادگی"></el-input>
                                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                                    </el-form-item>
                                    <el-form-item label="">
                                        <el-button v-loading.fullscreen.lock="fullscreenLoading" @click.prevent="saveName()"> ذخیره نام <span class="fa fa-user-plus"></span>
                                        </el-button>
                                    </el-form-item>
                                </el-form>
                            </dd>
                            <dt>تلفن همراه :</dt>
                            <dd v-text="user.mobile"></dd>
                            <dt>آدرس :</dt>
                            <dd v-if="user.address != null">
                                {{user.address.province_name}}  - {{user.address.city_name}} - {{user.address.address}}
                                <a href="#" @click.prevent="editAddress()"><span class="fa  fa-pencil-square-o "></span></a>
                            </dd>
                            <dd v-if="user.address == null"  :class="{'input-empty': errors.has('address_id') }">
                                <input  name="address_id"
                                        type="hidden"
                                          data-vv-as="آدرس"
                                          v-validate="'required'"
                                          v-model="orderInfo.address_id"
                                          placeholder="آدرس">
                                <span v-show="errors.has('address_id')">{{ errors.first('address_id') }}</span>
                                <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="addAddress()" size="small" round type="success">افزودن آدرس <span
                                        class="fa fa-map-marker"></span></el-button>
                            </dd>
                        </dl>
                    </div>

                    <hr>

                    <div class="row">
                        <h4 class="heading-order-part">لیست سفارشات</h4>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th> محصول</th>
                                    <th>قیمت واحد</th>
                                    <th>تعداد</th>
                                    <th>قیمت کل</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(product,index) in cartProducts">
                                    <td v-text="index+1"></td>
                                    <td>
                                        <div style="display: flex;align-items: center;">
                                        <span style="margin-left: 10px"><img :src="'/images/products/tn-'+product.image"
                                                                             class="img-thumbnail"
                                                                             alt="" width="70" height="70"></span>
                                            <a href="" style="display: flex;align-items: right;flex-direction: column;">
                                                <span v-text="product.name"></span>
                                                <span v-text="product.amount"></span>
                                            </a>
                                        </div>
                                    </td>
                                    <td v-text="formatPrice(product.price)"></td>
                                    <td>
                                        <el-input-number
                                                v-model="orderInfo.numberCartProducts[index]"
                                                size="mini"
                                                :min="convertToInt(product.min_order)"
                                                :max="convertToInt(product.quantity)">
                                        </el-input-number>
                                    </td>
                                    <td v-text="formatPrice(product.price*orderInfo.numberCartProducts[index])"></td>
                                </tr>
                                <tr>
                                    <td colspan="4">قیمت کل</td>
                                    <td v-text="totalPrice()"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <h4 class="heading-order-part">هزینه ارسال: &nbsp;&nbsp;0 تومان</h4>
                    </div>
                    <hr>
                    <div class="row">
                        <h4 class="heading-order-part">میخواهید توضیحی درباره سفارش دهید؟</h4>
                        <br>
                        <el-form size="mini">
                            <el-form-item :class="{'input-empty': errors.has('description') }" label="">
                                <el-input type="textarea" name="description" data-vv-as="توضیحات" v-validate="'min:10'"
                                          v-model="orderInfo.description" placeholder="توضیحات"></el-input>
                                <span v-show="errors.has('description')">{{ errors.first('description') }}</span>
                            </el-form-item>
                        </el-form>
                    </div>
                    <br>
                    <hr>
                    <br>

                    <div class="row">

                        <h4 class="heading-order-part">نحوه پرداخت</h4>

                        <div style="margin-top: 20px">
                            <el-radio-group v-model="orderInfo.payment" size="small">
                                <el-radio label="at_place" border>پرداخت در محل</el-radio>
                                &nbsp;
                                <el-radio label="online" border disabled>پرداخت آنلاین</el-radio>
                            </el-radio-group>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <el-button  v-loading.fullscreen.lock="fullscreenLoading" @click="submitOrders()" type="success" plain>ثبت سفارش
                        </el-button>
                    </div>
                </el-card>
            </div>
        </section>

        <el-dialog :fullscreen="true" :title="address.title" :visible.sync="address.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form label-position="top" size="small">
                    <el-form-item :class="{'input-empty': errors.has('province') }" label="استان :">
                        <el-select name="province" :default-first-option="true" data-vv-as="استان"
                                   v-validate="'required'" @change="getCities()"
                                   v-model="address.data.province"
                                   filterable
                                   placeholder="استان"
                                   no-match-text="استان را انتخاب کنید"
                                   no-data-text="استانی وجود ندارد">
                            <el-option
                                    v-for="province in provinces"
                                    :key="province.id"
                                    :label="province.name"
                                    :value="province.id">
                            </el-option>
                        </el-select>
                        <span v-show="errors.has('province')">{{ errors.first('province') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('city') }" label="شهر :">
                        <el-select name="city" data-vv-as="شهر" v-validate="'required'" @change="getCities()"
                                   v-model="address.data.city"
                                   filterable
                                   placeholder="شهر"
                                   no-match-text="شهر را انتخاب کنید"
                                   no-data-text="شهری وجود ندارد">
                            <el-option
                                    v-for="city in cities"
                                    :key="city.id"
                                    :label="city.name"
                                    :value="city.id">
                            </el-option>
                        </el-select>
                        <span v-show="errors.has('city')">{{ errors.first('city') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('local') }" label="منطقه :">
                        <el-input name="local" data-vv-as="منطقه" v-validate="'min:5'"
                                  v-model="address.data.local"></el-input>
                        <span v-show="errors.has('local')">{{ errors.first('local') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('address') }" label="آدرس :">
                        <el-input name="address" data-vv-as="آدرس" v-validate="'required|min:5'"
                                  v-model="address.data.address"></el-input>
                        <span v-show="errors.has('address')">{{ errors.first('address') }}</span>
                    </el-form-item>
                    <br>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button @click="saveAddress()" size="small" round type="success">ثبت آدرس<i
                                    class="el-icon-arrow-left el-icon-right"></i></el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    export default{
        data(){
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                provinces: [],
                orderInfo: {
                    name: '',
                    payment: 'at_place',
                    address_id: '',
                    description: '',
                    numberCartProducts: [],
                    idCartProducts: [],
                    types: [],
                },
                user: [],
                cities: [],
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
        mounted () {
            this.checkAuth();
            this.getProvinces();
            this.getUser();
            this.setCartProducts(this.cart);
        },
        methods: {
            checkAuth(){
                var vm = this;
                vm.openFullScreen();
                axios.get('/v1/users/check-auth').then(function (response) {
                    if(response.status == 200){
                        vm.auth = response.data == 'success'? true : false;
                        if (!vm.auth) {
                            vm.$router.go(-1);
                        }
                        vm.getUser();
                        vm.openFullScreen().close();
                    }
                })
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
            convertToInt(val){
                return parseInt(val)
            },
            openFullScreen() {
                const loading = this.$loading({
                    lock: true,
//                    text: 'درحال بار گذاری...',
                    spinner: 'fa fa-spinner fa-pulse fa-3x fa-fw',
                    background: 'rgba(0, 0, 0, 0.7)'
                });
                return loading;
            },
            addAddress(){
                this.address.operation = 'add'
                this.address.visible = true;
            },
            editAddress(){
                var add = this.user.address;
                var form = {
                    id: add.id,
                    province: '',
                    city: '',
                    local: add.region,
                    address: add.address,
                }

                this.address.data = form;
                this.address.operation = 'edit';
                this.address.visible = true;
            },
            saveName(){
                var vm = this;
                this.$validator.validateAll({name: vm.orderInfo.name}).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;
                        axios.post('/v1/users/name/store', {name: vm.orderInfo.name}).then(function (response) {
                            if (response.status == 200) {
                                vm.user.name = vm.orderInfo.name;
                                vm.fullscreenLoading = false;
                            }
                        }).catch(function (errors) {
                        })
                    }
                });
            },
            saveAddress(){
                var vm = this;
                var form = vm.address.data;
                var form2 = {
                    id: form.id,
                    province: form.province,
                    city: form.city,
                    local: form.local,
                    address: form.address,
                }

                this.$validator.validateAll(form2).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;
                        var url = this.address.operation == 'add' ? '/v1/addresses/store' : '/v1/addresses/update'
                        axios.post(url, vm.address.data).then(function (response) {
                            if (response.status == 200) {
                                vm.user.address = response.data;
                                vm.user.address_id = response.data.id;
                                vm.orderInfo.address_id = response.data.id;
                                vm.address.visible = false;
                                vm.fullscreenLoading = false;
                            }
                        })
                    }
                });
            },
            formatPrice(price){
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان ";
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
            submitOrders(){
                var vm = this;
                var form = vm.orderInfo;
                form.name = vm.user.name;
                form.address_id = vm.user.address_id;

                vm.$validator.validateAll(form).then((result) => {
                    if (form.numberCartProducts.length == 0 && form.numberCartProducts.length != form.idCartProducts)
                        vm.errors.items.push({msg: 'لیست سفارشات شما خالی است!!'})
                    if (result) {
                        vm.fullscreenLoading = true;
                        axios.post('/v1/orders/store', form).then(function (response) {
                            if (response.status == 200) {
                                if (response.data.status == 'online')
                                    window.location.href = response.data.link;
                                else{
                                    vm.$notify({
                                        title: 'پراخت',
                                        message: response.data.msg,
                                        type: 'success',
                                    });
                                    vm.$cookie.delete('cart');
                                    vm.$cookie.delete('numberProducts');
                                    window.location.href = '/dashboard';
                                }

                                vm.fullscreenLoading = false;
                            }
                        }).catch(function (errors) {
                            vm.$notify({
                                title: 'خطا',
                                message: errors.response.data.errors.message[0],
                                type: 'error',
                            });
                        });
                    }
                    else{
                        vm.$notify({
                            title: 'خطا',
                            message: vm.errors.items[0].msg,
                            type: 'error',
                        });
                    }
                }).catch(function (error) {
                    console.log(error)
                });
            },
            updateNumberProduct(numbers){
                this.orderInfo.numberCartProducts = numbers;
                this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
            },
            totalPrice(){
                var sum = 0;

                for (var i = 0; i < this.cartProducts.length; i++) {
                    sum += this.cartProducts[i].price * this.orderInfo.numberCartProducts[i];
                }
                return this.formatPrice(sum);
            },
            addCart(product,type){
                var ids = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                var numbers = this.$cookie.get('numberProducts') == null ? [] : JSON.parse(this.$cookie.get('numberProducts'));
                if (ids.indexOf(product.id) == -1 && types.indexOf(type) == -1) {
                    ids.push(product.id);
                    ids.push(type);
                    numbers.push(1);
                    this.cartProducts.push(product);
                    this.$cookie.set('cart', JSON.stringify(ids), 1);
                    this.$cookie.set('types', JSON.stringify(types), 1);
                    this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
                } else {
                    var indexId = ids.indexOf(product.id);
                    numbers[indexId] += 1;
                    this.orderInfo.numberCartProducts = numbers;
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
                    this.orderInfo.numberCartProducts.splice(index, 1);
                    this.$cookie.set('numberProducts', JSON.stringify(this.orderInfo.numberCartProducts), 1);
                    this.$cookie.set('cart', JSON.stringify(ids), 1);
                    this.$cookie.set('types', JSON.stringify(types), 1);
                }
            },
            setCartProducts(){
                var vm = this;
                this.orderInfo.idCartProducts = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                this.orderInfo.numberCartProducts = this.$cookie.get('numberProducts') == null ? [] : JSON.parse(this.$cookie.get('numberProducts'));
                this.orderInfo.types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                // if (this.orderInfo.numberCartProducts.length == 0 || this.orderInfo.idCartProducts.length ==0) {
                //     window.location.href = '/dashboard';
                // }

                axios.get(`/v1/products/get-cart-products/${JSON.stringify({ids:this.orderInfo.idCartProducts,types:this.orderInfo.types})}`).then(function (response) {
                    vm.cartProducts = response.data;
                })
            },
        }

    }
</script>

<style>
    .el-radio {
        margin-left: 50px;
    }

    tbody{
        font-size: 12px;
    }
    thead{
        font-size: 14px;
    }

    .el-button--success.is-plain {
        float: left;
    }
    #ordercart .el-card{
        margin:20px;
        padding:20px 5%;
    }
</style>