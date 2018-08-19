<template>
    <div>
        <el-card class="box-card order-list ">
            <div class="table-responsive">
            <table class="table table-bordered" v-loading="fullscreenLoading">
                <thead>
                <tr>
                    <th>کد سفارش</th>
                    <th> تاریخ</th>
                    <th>مبلغ</th>
                    <th>وضعیت پرداخت</th>
                    <th>وضعیت ارسال</th>
                    <th>جزئیات</th>
                </tr>
                </thead>
                <tbody v-for="orderList in orderLists">
                <tr>
                    <td v-text="orderList.id"></td>
                    <td v-text="changeDate(orderList.created_at)"></td>
                    <td v-text="formatPrice(orderList.price)"></td>
                    <td>
                        <div v-if="orderList.transaction">
                            <el-tag v-if="orderList.transaction.status == 1" type="success"  size="mini">پرداخت موفق</el-tag>
                            <el-tag v-else type="danger"   size="mini">پرداخت ناموفق</el-tag>
                        </div>
                    </td>
                    <td>
                        <el-tag type="info" v-if="orderList.status == 'pending'" size="mini">سفارش داده شده</el-tag>
                        <el-tag type="primary"  v-if="orderList.status == 'sent'" size="mini">ارسال شده</el-tag>
                        <el-tag type="danger"  v-if="orderList.status == 'canceled'" size="mini">لغو شده</el-tag>
                        <el-tag type="success"  v-if="orderList.status == 'delivered'" size="mini">تحویل داده شده</el-tag>
                        <el-tag type="warning"  v-if="orderList.status == 'confirmed'" size="mini">تایید شده</el-tag>
                    </td>
                    <td>
                        <el-button size="mini" data-toggle="collapse" :data-target="'#orderList'+orderList.id" round>
                            محصولات<span class="fa fa-chevron-down"></span></el-button>
                        <el-tooltip class="item" effect="dark" content="افزودن به سبد خرید" placement="top-start">
                            <a href="#" @click.prevent="addCart(orderList.cart)"><span
                                    class="fa fa-cart-arrow-down"></span></a>
                        </el-tooltip>
                    </td>
                </tr>
                <tr :id="'orderList'+orderList.id" class="collapse order-list-collapse">
                    <td colspan="6">
                        <table class="table table-bordered table-responsive">
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
                            <tr v-if="order.order_product != null" v-for="(order,index) in orderList.orders">
                                <td v-text="index+1"></td>
                                <td>
                                    <div style="display: flex;align-items: center;">
                                        <span style="margin-left: 10px"><img
                                                :src="'/images/products/tn-'+order.order_product.image"
                                                class="img-thumbnail"
                                                alt="" width="70" height="70"></span>
                                        <router-link :to="{name: 'product.detail',params:{title:replaceAll(order.order_product.name,' ', '-'),id:order.order_product.id,type:'product'}}">
                                            <span v-text="order.order_product.name"></span>
                                        </router-link>
                                        <a href="" style="display: flex;align-items: right;flex-direction: column;">
                                        </a>
                                    </div>
                                </td>
                                <td v-text="formatPrice(order.order_product.price)"></td>
                                <td v-text="order.quantity"></td>
                                <td v-text="formatPrice(order.quantity*order.unit_price)"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </el-card>
    </div>
</template>

<script>
    var moment = require('moment-jalaali');
    moment.loadPersian({usePersianDigits: true});

    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                orderLists: [],
                paginateOrder:[],
                filters: {
                    user_id: '',
                    province: '',
                    status: '',
                    city: '',
                    page: 1,
                    perPage: 15,
                },
            }
        },
        mounted(){
            if (!this.auth) {
                this.$router.go(-1);
            }
            if (this.checkRole('admin') != -1) {
                this.$router.push({name:'dashboard.profile'});
            }
            this.getOrders();
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            formatPrice(price){
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان ";
            },
            changeDate(date){
                return moment(date, 'YYYY-M-D HH:mm:ss').endOf('jMonth').format('jYYYY/jM/jD HH:mm:ss') // 1392/6/31 23:59:59
            },
            getOrders(){
                var vm = this;
                vm.fullscreenLoading = true;
                axios.get('/v1/orders/get-orders',{params:vm.filters}).then(function (response) {
                    vm.orderLists = response.data.data;
                    vm.paginateOrder = response.data;
                    vm.fullscreenLoading = false;
                })
            },
            addCart(cart){
                var ids = this.$cookie.get('cart') == null ? [] : JSON.parse(this.$cookie.get('cart'));
                var numbers = this.$cookie.get('numberProducts') == null ? [] : JSON.parse(this.$cookie.get('numberProducts'));
                var types = this.$cookie.get('types') == null ? [] : JSON.parse(this.$cookie.get('types'));
                for(var i=0;i<cart.ids.length;i++){
                    var status = false, indexNumber = '';
                    for (var index in types) {
                        if (types[index] == 'product' && ids[index] == cart.ids[i]) {
                            status = true
                            indexNumber = index;
                            break;
                        }

                    }
                    if(!status){
                        types.push('product');
                        ids.push(cart.ids[i]);
                        numbers.push(cart.numbers[i]);
                    }else{
                        numbers[i] = parseInt(cart.numbers[i]) + parseInt(numbers[i]) ;
                    }
                }
                this.$cookie.set('cart',JSON.stringify(ids),1);
                this.$cookie.set('types', JSON.stringify(types), 1);
                this.$cookie.set('numberProducts',JSON.stringify(numbers),1);
                this.$message({
                    type: 'info',
                    message: 'محصولات به لیست سفارشات اضافه شد.'
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