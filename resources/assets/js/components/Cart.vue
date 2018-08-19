<template>
    <div>
        <div class="row">
            <el-card class="box-card">
                <div slot="header" class=" cart-header">

                    <div>
                        <span class="ion-ios-cart-outline"></span>&nbsp;&nbsp;
                        <span class="text-cart"> سفارشات</span>
                    </div>

                    <span class="label label-success">{{products.length}}</span>

                </div>
                <div>
                    <div>
                        <div v-for="(product,index) in products" :key="index" class="product-box">
                            <div class="product-in-cart">
                                <span @click="removeCart(index)" class="animated delete-icon fa fa-trash-o"></span>
                                <p>
                                    <router-link :to="{name: 'product.detail',params:{title:replaceAll(product.name,' ', '-'),id:product.id,type:product.type}}">
                                        <span class="btn product-title" v-if="product.type == 'product'" v-text="product.name"></span>
                                        <span class="btn product-title" v-if="product.type == 'package'">{{product.name}} (پکیج)</span>
                                    </router-link>
                                </p>
                                <div class="">
                                    <span class="pull-right product-price" v-text="formatPrice(product.price)"></span>
                                    <span class="pull-left">
                            <el-input-number @input="changeNumber()" v-model="numberProducts[index]" size="mini"
                                             :min="product.type=='product'?convertToInt(product.min_order):1" :max="convertToInt(product.quantity)"></el-input-number>
                            </span>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-4">
                            <a href="" class="cart_anchor"></a>
                        </div>

                        <div class="col-md-2"></div>
                    </div>
                    <div class="cart-footer" v-if="products.length>0">
                        <div class="crow">
                            <span>قیمت کل</span>
                            <span v-text="totalPrice()"></span>
                        </div>
                        <div class="crow">
                            <span></span>
                            <router-link :to="auth? {name : 'checkout'}:{name:'pages.login'}">
                                <el-button type="success" round>ثبت سفارش</el-button>
                            </router-link>
                            <!--<a :href="auth ? '/checkout' : '/login'">-->
                                <!--<el-button type="success" round>بررسی و ثبت سفارش</el-button>-->
                            <!--</a>-->
                            <span></span>

                        </div>
                    </div>
                    <div class="cart-footer" v-else>
                        <div class="crow">
                            <p style="font-size:12px;text-align: center"> شما سفارشی ندارید.</p>
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
    </div>


</template>

<script>
    export default {
        props: ['products', 'removeCart', 'numberProducts'],
        data(){
            return {
                auth: window.Laravel.auth,
                numbers: [],
            }
        },
        mounted() {
            var ids = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
            var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
            var numbers = this.$cookie.get('numberProducts') == null ? [] : JSON.parse(this.$cookie.get('numberProducts'));
            if (ids.length != numbers.length && ids.length != types.length ) {
                this.$cookie.delete('cart')
                this.$cookie.delete('numberProducts')
                this.$cookie.delete('types')
            }
        },
        methods: {
            replaceAll(str, find, replace) {
                if(str)
                return str.replace(new RegExp(find, 'g'), replace);
            },
            convertToInt(val){
                return parseInt(val)
            },
            formatPrice(price){
                if (price != "")
                    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان ";
                return "";
            },
            changeNumber(){
                this.$emit('update-number', this.numberProducts);
            },
            totalPrice(){
                var sum = 0
                for (var i = 0; i < this.products.length; i++) {
                    sum += this.products[i].price * this.numberProducts[i];
                }
                return this.formatPrice(sum);
            },


        }
    }
</script>


<style>
    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ion-ios-cart-outline {
        font-size: 25px;
    }

    .el-card__header {
        padding: 5px 15px;
        background-color: #ccc;
        color: white;
    }

    .el-card__body {
        padding: 10px 0;
    }

    .product-in-cart {
        display: flex;
        flex-direction: column;
        justify-content: start;
        padding: 0 15px;
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }

    .product-box {
        margin: 0 !important;
    }

    .product-box:hover {
        border-right: 1px solid red;
    }

    .delete-icon {
        color: red;
    }

    .cart-footer > .crow {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 15px !important;
        margin: 15px 0;
    }

    .product-title {
        font-size: 12px;
        margin: 0 !important;
        padding: 0 !important;
    }

    .product-price {
        font-size: 12px;
    }

    .cart-footer > .crow:nth-child(2) {
        justify-content: center;
    }

    .el-input-number--mini {
        width: 100px;
    }

    hr {
        margin: 0 !important;
    }

    .delete-icon {
        cursor: pointer;
        font-size: 18px;
    }

    .animated {
        animation-duration: 1s;
        animation-fill-mode: both;
    }

    @keyframes shake {
        from, to {
            transform: translate3d(0, 0, 0);
        }

        10%, 30%, 50%, 70%, 90% {
            transform: translate3d(-10px, 0, 0);
        }

        20%, 40%, 60%, 80% {
            transform: translate3d(10px, 0, 0);
        }
    }

    @keyframes jello {
        from, 11.1%, to {
            transform: none;
        }

        22.2% {
            transform: skewX(-12.5deg) skewY(-12.5deg);
        }

        33.3% {
            transform: skewX(6.25deg) skewY(6.25deg);
        }

        44.4% {
            transform: skewX(-3.125deg) skewY(-3.125deg);
        }

        55.5% {
            transform: skewX(1.5625deg) skewY(1.5625deg);
        }

        66.6% {
            transform: skewX(-0.78125deg) skewY(-0.78125deg);
        }

        77.7% {
            transform: skewX(0.390625deg) skewY(0.390625deg);
        }

        88.8% {
            transform: skewX(-0.1953125deg) skewY(-0.1953125deg);
        }
    }

    .delete-icon:hover {
        animation-name: jello;
        transform-origin: center;
    }

    .cart-footer a {
        text-decoration: none;
        color: #fff;
    }
</style>