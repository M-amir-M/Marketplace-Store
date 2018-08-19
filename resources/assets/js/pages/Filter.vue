<template>
    <section id="home-main">
        <div class="container">
            <section id="main-products">
                <div class="col-sm-2 col-xs-12 hidden-xs left-aside">
                    <cart :number-products="numberCartProducts" @update-number="updateNumberProduct($event)"  :remove-cart="removeCart"  :products="cartProducts"></cart>
                </div>
                <div class="col-sm-8 col-xs-12 main-content">
                    <div class="row">
                        <el-card class="box-card">
                            <div slot="header">
                                <div>
                                    <span class="fa fa-search"></span>&nbsp;&nbsp;
                                    <span> {{title}}</span>
                                </div>
                            </div>
                            <div class="search-products">
                                <div class="row" v-for="chunk in chunkedProducts">
                                    <div class="search-row col-md-3 col-sm-4 col-xs-6" v-for="product in chunk">
                                        <product :add-cart="addCart" :product="product"></product>
                                    </div>
                                </div>
                            </div>
                        </el-card>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <br><br>
                            <div class="block text-center">
                                <el-pagination
                                        background
                                        layout="prev, pager, next"
                                        :total="paginateProduct.total"
                                        :page-size="paginateProduct.per_page"
                                        :current-page="filters.page"
                                        @current-change="changePage"
                                >
                                </el-pagination>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 col-xs-12 hidden-sm hidden-md hidden-lg left-aside">
                    <cart :number-products="numberCartProducts" @update-number="updateNumberProduct($event)"  :remove-cart="removeCart"  :products="cartProducts"></cart>
                </div>
                <div class="col-sm-2 hidden-xs  right-aside">
                    <el-card class="box-card brand-card">
                        <div slot="header" class="cart-header">

                            <div>
                                <span class=""></span>&nbsp;&nbsp;
                                <span class="text-cart"> برند</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <el-checkbox-group v-model="filters.brand_ids" @change="changeBrand" text-color="#ccc">
                                <el-checkbox v-for="(brand,index) in brands" :label="brand.id" :key="index">{{brand.name}}</el-checkbox>
                            </el-checkbox-group>
                        </div>
                    </el-card>
                </div>
            </section>
        </div>
    </section>

</template>

<script>
    import Product from './../components/Product.vue';


    export default {
        components: {
            'product': Product
        },
        data(){
            return {
                provinces: [],
                cities: [],
                paginateProduct: [],
                cartProducts: [],
                numberCartProducts: [],
                idCartProducts: [],
                title:'',
                brands:[],
                filters : {
                    term : '',
                    page : 1,
                    category_ids : '',
                    brand_ids : [],
                }
            }
        },
        mounted () {
            var fn = this.$route.name;
            if(fn == 'filter.search'){
                this.filters.term = this.$route.params.term;
                this.title = `جستجو برای ${this.filters.term}`;
                this.getFilterProduct();
            }
            if(fn == 'filter.category'){
                this.filters.category_ids = this.$route.params.category_id;
                this.title = this.replaceAll(this.$route.params.category_name,'-',' ');
                this.getFilterProduct();
            }
            if(fn == 'filter.brand'){
                this.filters.brand_ids = [this.$route.params.brand_id];
                this.title = this.replaceAll(this.$route.params.brand_name,'-',' ');
                this.getFilterProduct();
            }

            this.setCartProducts();
        },
        watch: {
            '$route' (to, from) {
                if(to.name == 'filter.search'){
                    this.filters.brand_ids = [];
                    this.filters.term = to.params.term;
                    this.title = `جستجو برای ${this.filters.term}`;
                }
                if(to.name == 'filter.category'){
                    this.filters.brand_ids = [];
                    this.filters.category_ids = to.params.category_id;
                    this.title = this.replaceAll(to.params.category_name,'-',' ');
                }
                if(to.name == 'filter.brand'){
                    this.filters.brand_ids = [to.params.brand_id];
                    this.title = this.replaceAll(to.params.brand_name,'-',' ');
                }

                this.getFilterProduct();
            }
        },
        computed: {
            chunkedProducts() {
                var column = 4;
                if (screen.width < 768 && screen.width > 0)
                    column = 2;
                if(screen.width > 768 && screen.width < 992)
                    column = 3;
                if(screen.width > 992)
                    column = 4;
                return _.chunk(this.paginateProduct.data, column);
            }
        },
        methods: {
            openFullScreen() {
                const loading = this.$loading({
                    lock: true,
//                    text: 'درحال بار گذاری...',
                    spinner: 'fa fa-spinner fa-pulse fa-3x fa-fw',
                    background: 'rgba(0, 0, 0, 0.7)'
                });
                return loading;
            },
            changePage(page){
                this.filters.page = page;
                this.getFilterProduct();
            },
            changeBrand(val){
                this.filters.brand_ids = val;
                this.getFilterProduct();
            },
            getFilterProduct(){
                var vm = this;
                vm.openFullScreen()
                var fi = vm.filters;
                axios.get(`/v1/products/filter/term=${fi.term}/page=${fi.page}/cats=${fi.category_ids}/brands=${fi.brand_ids.join('-')}`).then(function (response) {
                    vm.paginateProduct = response.data['products'];
                    vm.brands = response.data['brands'];
                    vm.openFullScreen().close()
                })
            },
            updateNumberProduct(numbers){
                this.numberCartProducts = numbers;
                this.$cookie.set('numberProducts',JSON.stringify(numbers),1);
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

                for(var i = 0; i < this.cartProducts.length; i++){
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
                var ids = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                var type = types[index];
                if(ids.indexOf(product.id) != -1 && types.indexOf(type) != -1){
                    var idIndex = ids.indexOf(product.id);
                    ids.splice(idIndex, 1);
                    types.splice(idIndex, 1);
                    this.cartProducts.splice(index, 1);
                    this.numberCartProducts.splice(index, 1);
                    this.$cookie.set('numberProducts',JSON.stringify(this.numberCartProducts),1);
                    this.$cookie.set('cart',JSON.stringify(ids),1);
                    this.$cookie.set('types',JSON.stringify(types),1);
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
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },

        }
    }
</script>

<style>
    .right-aside{
        padding-left: 0 ;
        padding-right: 0 ;
    }
    .el-checkbox, .el-checkbox__input {
        position: static;
    }
    .brand-card .el-checkbox__label {
        padding:0;
        padding-right: 10px;
        font-size: 12px;
    }
    .brand-card .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .brand-card .el-card__header {
        padding: 5px 15px;
        background-color: #ccc;
        color: white;
    }

    .brand-card .el-card__body {
        padding: 10px 5px;
    }

    .search-row{
        margin-bottom: 10px;
        margin-top: 10px;
    }

    .main-content{
        margin:0 10px;
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




    @media (max-width: 768px) {
        #main-products{
            display: block;
        }
        #main-products .main-aside {
            padding-right: 30px;
            padding-left: 30px;
        }

        #main-products .main-content {
            padding: 5px;
            padding-right:25px;
        }
        .search-row{
            margin-bottom: 5px;
            margin-top: 5px;
            padding: 5px;
        }
        .main-content .el-card__body {
            padding: 0 !important;;
        }
    }
</style>