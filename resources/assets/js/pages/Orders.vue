<template>
    <div class="orders-page">
        <div class="row">
            <el-card class="box-card order-list ">
                <div class="filter-section">
                    <el-select
                            v-if="checkRole(['admin'])"
                            size="small"
                            v-model="filters.province"
                            placeholder="استان"
                            @change="filter()">
                        <el-option label="همه" value=""></el-option>
                        <el-option v-for="province in provinces"
                                   :label="province.name"
                                   :key="province.id"
                                   :value="province.id"></el-option>
                    </el-select>
                    &nbsp
                    <el-select
                            v-if="filters.province != ''"
                            size="small"
                            v-model="filters.city"
                            @change="getOrders()"
                            placeholder="شهر">
                        <el-option label="همه" value=""></el-option>
                        <el-option v-for="city in cities"
                                   :label="city.name"
                                   :key="city.id"
                                   :value="city.id"></el-option>
                    </el-select>
                    &nbsp
                    <el-select
                            size="small"
                            v-model="filters.status"
                            @change="getOrders()"
                            placeholder="شهر">
                        <el-option label="همه" value=""></el-option>
                        <el-option label="سفارش داده شده" value="pending"></el-option>
                        <el-option label="ارسال شده" value="sent"></el-option>
                        <el-option label="لغو شده" value="canceled"></el-option>
                        <el-option label="تحویل داده شده" value="delivered"></el-option>
                        <el-option label="تایید شده" value="confirmed"></el-option>
                    </el-select>
                    &nbsp
                </div>
                <div>
                    <p v-if="paginateOrder.total">تعداد سفارش : {{paginateOrder.total}}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" v-loading="loading">
                        <thead>
                        <tr>
                            <th>کد سفارش</th>
                            <th> تاریخ</th>
                            <th>مبلغ</th>
                            <th>وضعیت پرداخت</th>
                            <th>وضعیت ارسال</th>
                            <th>مشاهده</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody v-if="orderLists.length >0 " v-for="(orderList,index) in orderLists">
                        <tr>
                            <td v-text="orderList.id"></td>
                            <td v-text="formatDate(orderList.created_at)"></td>
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
                            </td>

                            <td>
                                <el-tooltip v-if="orderList.status == 'pending'" class="item" effect="dark" content="تایید سفارش" placement="top-start">
                                    <el-button @click="changeStatus(orderList.id,'confirmed',index)" type="warning" size="mini" plain round icon="fa fa-check-square-o"></el-button>
                                </el-tooltip>
                                <el-tooltip v-if="orderList.status == 'pending'" class="item" effect="dark" content="لغو سفارش" placement="top-start">
                                    <el-button @click="changeStatus(orderList.id,'canceled',index)" type="danger" plain size="mini" round icon="fa fa-trash-o"></el-button>
                                </el-tooltip>
                                <el-tooltip v-if="orderList.status == 'confirmed'"  class="item" effect="dark" content="ارسال سفارش" placement="top-start">
                                    <el-button @click="changeStatus(orderList.id,'sent',index)" type="primary" size="mini" plain round icon="fa fa-truck"></el-button>
                                </el-tooltip>
                                <el-tooltip v-if="orderList.status == 'delivered'"  class="item" effect="dark" content="ارسال سفارش" placement="top-start">
                                    <el-button @click="changeStatus(orderList.id,'sent',index)" type="primary" size="mini" plain round icon="fa fa-truck"></el-button>
                                </el-tooltip>
                                <el-tooltip v-if="orderList.status == 'canceled'"  class="item" effect="dark" content="تایید سفارش" placement="top-start">
                                    <el-button @click="changeStatus(orderList.id,'confirmed',index)" plain type="warning" size="mini" round icon="fa fa-check-square-o"></el-button>
                                </el-tooltip>
                                <el-tooltip v-if="orderList.status == 'sent'"  class="item" effect="dark" content="تحویل داده شده" placement="top-start">
                                    <el-button @click="changeStatus(orderList.id,'delivered',index)" plain type="success" size="mini" round icon="fa fa-check"></el-button>
                                </el-tooltip>

                            </td>
                        </tr>
                        <tr :id="'orderList'+orderList.id" class="collapse order-list-collapse">
                            <td colspan="7">
                                <div v-if="orderList.transaction" class="panel panel-default empty-table-msg">
                                    <div class="panel-body">
                                        <p class="text-success">شماره فاکتور : {{orderList.id}}</p>
                                        <p class="text-success">کد پیگیری : {{orderList.transaction.return_code}}</p>
                                        <p class="text-success">شماره مرجع : {{orderList.transaction.track_code}}</p>
                                    </div>
                                </div>
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
                                        <span style="margin-left: 10px"><img :src="'/images/products/tn-'+order.order_product.image"
                                                                             class="img-thumbnail"
                                                                             alt="" width="70" height="70"></span>
                                                <a href="" style="display: flex;align-items: right;flex-direction: column;">
                                                    <span v-text="order.order_product.name"></span>
                                                    <span v-text="order.order_product.amount"></span>
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
        <div class="row">
            <br>
            <div class="block text-center">
                <el-pagination
                        background
                        layout="prev, pager, next"
                        :total="paginateOrder.total"
                        :page-size="paginateOrder.per_page"
                        :current-page="filters.page"
                        @current-change="changePage">
                </el-pagination>


            </div>
        </div>
    </div>
</template>


<script>

    var moment = require('moment-jalaali');
    moment.loadPersian({usePersianDigits: true});

    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                loading:false,
                paginateOrder:[],
                orderLists: [],
                provinces: [],
                cities: [],
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
            if(!this.auth){
                this.$router.go(-1);
            }
            if(this.checkRole('admin')==-1){
                this.$router.go(-1);
            }
            this.filters.user_id = this.$route.params.id;
            this.getProvinces();
            this.getOrders();
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            changePage(page){
                this.filters.page = page;
                this.getOrders();
            },
            filter(){
                this.filters.city = '';
                this.getCities();
                this.getOrders();
            },
            getProvinces(){
                var vm = this;
                axios.get('/v1/users/get-provinces').then(function (response) {
                    vm.provinces = response.data;
                })
            },
            getCities(){
                var vm = this;
                axios.get(`/v1/users/get-cities/${vm.filters.province}`).then(function (response) {
                    vm.cities = response.data;
                })
            },
            formatPrice(price){
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+" تومان ";
            },
            formatDate(date){
                return moment(date, 'YYYY-M-D HH:mm:ss').
                format('jYYYY/jM/jD HH:mm:ss') // 1392/6/31 23:59:59
            },
            getOrders(){

                var vm = this;
                vm.loading = true;
                axios.get('/v1/orders/get-orders',{params:vm.filters}).then(function (response) {
                    vm.orderLists = response.data.data;
                    vm.paginateOrder = response.data;
                    vm.loading = false;
                })
            },
            status(status){
                if(status == 'pending'){
                    return 'ثبت شده'
                }if(status == 'confirmed'){
                    return 'تایید شده'
                }if(status == 'sent'){
                    return 'ارسال شده'
                }if(status == 'canceled'){
                    return 'لغو شده'
                }
            },
            changeStatus(id,status,index){
                var vm = this;
                vm.loading = true;
                axios.post('/v1/orders/change-status',{id:id,status:status}).then(function (response) {
                    if(response.status == 200){
                        vm.orderLists[index].status = status;
                        vm.loading= false;
                    }
                }).catch(function (errors) {
                    vm.loading = false;
                })
            }
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
    .orders-page .filter-section{
        display: flex;
        margin : 10px 0;
    }
</style>