<template>
    <div id="homepage">
        <section id="home-main">
            <div class="container">
                <section id="main-carousel">
                    <el-carousel indicator-position="inside">
                        <el-carousel-item v-for="(carousel,index) in carousels" :key="index">
                            <img height="100%" width="100%" :src="'/images/carousels/'+carousel.image" class="img-responsive">
                        </el-carousel-item>
                    </el-carousel>
                </section>
                <section id="main-products">
                    <div class="col-sm-9 col-sm-push-3 main-content">
                        <div class="row" v-for="product in products" v-if="product[1].length>0">
                            <products :add-cart="addCart"
                                      :products="product[1]"
                                      :header-title="product[0].name"
                                      header-icon=""></products>
                            <br>
                        </div>
                    </div>
                    <div class="col-sm-3 col-sm-pull-9 main-aside">
                        <cart :number-products="numberCartProducts" v-on:update-number="updateNumberProduct($event)"
                              :remove-cart="removeCart" :products="cartProducts"></cart>
                        <div class="info-block row" v-if="infos.length>0">
                            <el-card class="box-card" v-for="(info,index) in infos" :key="index">
                                <p v-text="info.info"></p>
                            </el-card>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>

</template>

<script>

    export default{
        data(){
            return {
                carousels: [],
                infos :[],
                products: [],
                provinces: [],
                cities: [],
                cartProducts: [],
                numberCartProducts: [],
                idCartProducts: [],
            }
        },
        mounted () {
            this.getProducts()
            this.getActiveInfos();
            this.setCartProducts();
            this.getCarousel();
        },
        methods: {
            getActiveInfos(){
                var vm = this;
                axios.get('/v1/informations/get-active-infos').then(function (response) {
                    vm.infos = response.data;
                });
            },
            getCarousel(){
                var vm = this;
                axios.get('/v1/carousels/get-carousels').then(function (response) {
                    vm.carousels = response.data;
                });
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
            getProducts(){
                var vm = this;
                vm.openFullScreen();
                axios.get('/v1/products/get-home-products').then(function (response) {
                    vm.products = response.data;
                    vm.openFullScreen().close();
                })
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
        }
    }
</script>

<style>
    .main-content{
        margin: 0 -5px;
        padding-right: 23px;
    }

    #main-products {
        display: flex;
        justify-content: space-between;
        flex-direction: row-reverse;
    }

    #main-products .main-aside {
        padding-right: 50px;
        padding-left: 13px;
    }

    .info-block{
        font-size: 13px;
        margin-bottom:10px;
    }
    .info-block .el-card__body{
        padding: 10px;
    }
    .info-block .el-card{
        margin-top:10px;
    }

    .el-carousel__item{
        display: block !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .el-carousel {
        border-radius: 5px !important;
    }

    @media (max-width: 768px) {
        #homepage #main-products .main-content{
            margin: 5px 14px;
        }
        #main-products{
            display: block;
        }
        #main-products .main-aside {
            padding-right: 14px;
            padding-left: 14px;
        }

        #main-products .main-content {
            padding: 5px;
            padding-right: 15px;
            padding-left: 15px;
        }
        .search-row{
            margin-bottom: 5px;
            margin-top: 5px;
            padding: 5px;
        }
        .main-content .el-card__body {
            padding: 0 !important;;
        }

        #main-carousel .el-carousel__container {
            position: relative;
            height: 75px;
        }
    }
</style>