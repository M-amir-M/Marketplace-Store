<template>
    <div>
        <div class="row">
            <div class="container product-container">
                <div class="col-sm-9 col-sm-push-3 main-content">
                    <div>
                        <div class="row product-card">
                            <el-card class="box-card  info-card">
                                <div v-if="product_type == 'product'" class="row ">
                                    <br>
                                    <div class="col-sm-5 col-sm-push-7 product-image">
                                        <el-card :body-style="{ padding: '0px' }">
                                            <img :src="'/images/products/'+product.image" class="image" width="100%"
                                                 height="100%">
                                        </el-card>
                                    </div>
                                    <div class="col-sm-7 col-sm-pull-5 product-info">
                                        <el-card class="box-card head-info">
                                            <span v-text="product.name"></span>
                                            <el-tooltip v-if="auth"
                                                        :content="favorite ? 'حذف از علاقه مندی ها':'اضافه به علاقه مندی ها'"
                                                        placement="top">
                                                <span @click="changeFavorite()"
                                                      :class="favorite ? 'fa-heart':'fa-heart-o'"
                                                      class="icon fa"></span>
                                            </el-tooltip>
                                            <el-tooltip v-if="!auth" content="شما ثبت نام نکردید." placement="top">
                                                <span class="icon fa fa-heart-o"></span>
                                            </el-tooltip>
                                        </el-card>
                                        <div class="info" v-text="product.amount"></div>
                                        <div class="info">تولید کننده: {{product.brand.name}}</div>
                                        <div class="info">وضعیت: {{product.quantity>0 ? 'موجود' : 'نا موجود'}}</div>
                                        <div class="info">قیمت: {{product.price}}</div>
                                        <div class="info">توضیحات : {{product.description}}</div>
                                        <div class="info">
                                            <el-rate v-if="!auth || !buy"
                                                     v-model="product.rate"
                                                     disabled
                                                     show-score
                                                     text-color="#ff9900"
                                                     score-template="{value} امتیاز">
                                            </el-rate>
                                            <el-rate
                                                    @change="changeRate()"
                                                    v-if="auth && buy"
                                                    v-model="rate"
                                                    :texts="['خیلی بد بود', 'بد بود', 'راضی هستم', 'خوب بود', 'خیلی خوب بود']"
                                                    show-text>
                                            </el-rate>
                                        </div>
                                        <div class="add-to-cart">
                                            <button @click="addCart(product,'product')" type="text"
                                                    class="btn btn-block btn-primary">
                                                <span class="fa fa-plus"></span>&nbsp;افزودن به سفارشات
                                            </button>
                                        </div>

                                    </div>
                                    <br>
                                </div>
                                <div v-if="product_type == 'package'" class="row  package">
                                    <div class="col-xs-1"></div>
                                    <div class="col-xs-10">
                                        <div class="package-info info">
                                            <span>اسم پکیج: {{pack.name}}</span>
                                            <span class="add-to-cart">
                                                <button @click="addCart(pack,'package')" type="text"
                                                        class="btn btn-block btn-primary">
                                                    <span class="fa fa-plus"></span>&nbsp;افزودن به سفارشات
                                                </button>
                                            </span>
                                            <span>قیمت پکیج: {{formatPrice(pack.price)}}</span>
                                        </div>
                                        <div class="info" v-for="product in pack.products">
                                            <router-link
                                                    :to="{name: 'product.detail',params:{title:replaceAll(product.name,' ', '-'),id:product.id,type:'product'}}">
                                                <img :src="'/images/products/tn-'+product.image"
                                                     class="image img-thumbnail img-responsive"
                                                     style="max-height: 200px; border-bottom:1px solid #8c8c8c">
                                                &nbsp;
                                                <span v-text="product.name"></span>
                                                <span>قیمت محصول: {{formatPrice(product.price)}}</span>
                                            </router-link>
                                        </div>
                                    </div>
                                    <div class="col-xs-1"></div>
                                </div>
                            </el-card>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3 col-sm-pull-9 main-aside">
                    <cart :number-products="numberCartProducts" @update-number="updateNumberProduct($event)"
                          :remove-cart="removeCart" :products="cartProducts"></cart>
                </div>
            </div>

        </div>
    </div>
</template>


<script>
    import Products from './../components/Products.vue'

    export default{
        components: {Products},
        data(){
            return {
                auth: window.Laravel.auth,
                rate: '',
                product: {},
                pack: {},
                favorite: '',
                buy: false,
                product_id: '',
                cartProducts: [],
                numberCartProducts: [],
                idCartProducts: [],
                product_type: '',
            }
        },
        watch: {
            '$route' (to, from) {
                this.product_id = to.params.id;
                this.product_type = to.params.type;
                if (this.product_type == 'product')
                    this.getProduct();
                if (this.product_type == 'package')
                    this.getPackage();
            }
        },
        mounted(){
            this.product_id = this.$route.params.id;
            this.product_type = this.$route.params.type;
            if (this.product_type == 'product')
                this.getProduct();
            if (this.product_type == 'package')
                this.getPackage();
            this.setCartProducts();

        },
        methods: {
            changeFavorite(){
                var vm = this;
                vm.favorite = !vm.favorite;
                axios.post('/v1/products/change-favorite', {
                    favorite: vm.favorite,
                    product: vm.product.id
                }).then(function (response) {
                    if (response.status == 200) {
                        console.log(response.data);
                    }
                })
            },
            changeRate(){
                var vm = this;
                axios.post('/v1/products/change-rate', {
                    rate: vm.rate,
                    product: vm.product.id
                }).then(function (response) {
                    if (response.status == 200) {
                        vm.$message({
                            message: 'امتیاز شما با موفقیت ثبت شد.',
                            type: 'success',
                            duration: 5000
                        });
                    }
                })
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
            formatPrice(price){
                if (price != "")
                    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان ";
                return "";
            },
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            getProduct(){
                var vm = this;
                vm.openFullScreen();
                axios.get(`/v1/products/get-product/${vm.product_id}`).then(function (response) {
                    vm.product = response.data['product'];
                    vm.favorite = response.data['favorite'];
                    vm.buy = response.data['buy'];
                    vm.rate = parseFloat(response.data['rate']);
                    vm.openFullScreen().close();
                })
            },
            getPackage(){
                var vm = this;
                vm.openFullScreen();
                axios.get(`/v1/packages/get-package/${vm.product_id}`).then(function (response) {
                    vm.pack = response.data;
                    vm.openFullScreen().close();
                })
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
                axios.get(`/v1/products/get-cart-products/${JSON.stringify({
                    ids: this.idCartProducts,
                    types: types
                })}`).then(function (response) {
                    vm.cartProducts = response.data;
                })
            },
        }
    }
</script>

<style>
    .product-container {
        margin-top: 10px;
    }

    .head-info .el-card__body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
    }

    .head-info .icon {
        color: red;
        font-size: 20px;
        cursor: pointer;
    }

    .head-info {
        box-shadow: 0 2px 0px 0 rgba(0, 0, 0, .1);
    }

    .info {
        padding: 15px 0;
        border-bottom: 1px dashed #ccc;
    }

    .info-card > .el-card__body {
        padding: 15px;
    }

    .main-content {
        margin: 0 -5px;
    }

    .main-aside {
        padding-right: 30px;
        padding-left: 25px;
    }

    .package img {
        width: 100px;
        height: 100px;
    }

    .package a, .package-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .product-card {
            margin: 0 7px;
        }

        .product-info {
            margin: 0 2px;
        }

        .main-aside {
            padding-right: 30px;
            padding-left: 30px;
        }

        .main-content {
            padding: 5px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .main-content .el-card__body {
            padding: 0 !important;;
        }

    }
</style>