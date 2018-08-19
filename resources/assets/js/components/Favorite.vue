<template>
    <div>
        <el-card class="box-card order-list">
            <table class="table table-bordered table-responsive" v-loading="fullscreenLoading">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th> محصول</th>
                    <th>مبلغ</th>
                    <th>برند</th>
                    <th>جزئیات</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(favorite,index) in favorites">
                    <td v-text="index+1"></td>
                    <td>
                        <div style="display: flex;align-items: center;">
                                        <span style="margin-left: 10px"><img
                                                :src="'/images/products/tn-'+favorite.image"
                                                class="img-thumbnail"
                                                alt="" width="70" height="70"></span>
                            <router-link :to="{name: 'product.detail',params:{title:replaceAll(favorite.name,' ', '-'),id:favorite.id,type:'product'}}">
                                <span v-text="favorite.name"></span>
                            </router-link>
                            <a href="" style="display: flex;align-items: right;flex-direction: column;">
                            </a>
                        </div>
                    </td>
                    <td v-text="formatPrice(favorite.price)"></td>
                    <td v-text="favorite.brand.name"></td>
                    <td>
                        <el-tooltip class="item" effect="dark" content="افزودن به سبد خرید" placement="top-start">
                            <el-button size="mini" round plain @click.prevent="addCart(favorite,'product')"><span
                                    class="fa fa-cart-arrow-down"></span></el-button>
                        </el-tooltip>
                    </td>
                </tr>
                </tbody>
            </table>
        </el-card>
    </div>
</template>

<script>
    var moment = require('moment-jalaali');
    moment.loadPersian({usePersianDigits: true});

    export default {
        data(){
            return {
                fullscreenLoading: false,
                favorites: []
            }
        },
        mounted(){
            this.getFavorite();
        },
        methods: {
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            formatPrice(price){
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان ";
            },
            getFavorite(){
                var vm = this;
                vm.fullscreenLoading = true;
                axios.get('/v1/products/get-favorites').then(function (response) {
                    vm.favorites = response.data;
                    console.log(vm.favorites)
                    vm.fullscreenLoading = false;
                })
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
                    console.log(1);
                    ids.push(product.id);
                    types.push(type);
                    numbers.push(1);
//                    this.cartProducts.push(product);
                    this.$cookie.set('cart', JSON.stringify(ids), 1);
                    this.$cookie.set('types', JSON.stringify(types), 1);
                    this.$cookie.set('numberProducts', JSON.stringify(numbers), 1);
                    this.setCartProducts();
                    this.$parent.cartNumber = numbers.length;
                } else {
                    console.log(2)
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
        }
    }
</script>

<style scoped>

    .status .panel-title {
        font-size: 72px;
        font-amount: bold;
        color: #fff;
        line-height: 45px;
        padding-top: 20px;
        letter-spacing: -0.8px;
        padding-bottom: 25px;
    }

    tbody {
        font-size: 12px;
    }

    .box-card {
        margin-bottom: 15px;
    }
</style>