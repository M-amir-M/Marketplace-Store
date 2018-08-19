<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">محصولات
                        <div class="pull-left table-head">
                            <el-input
                                    size="small"
                                    placeholder="جستجوی محصول"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.term"
                                    @input="getFilterProduct()"
                            >
                            </el-input>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                v-loading="loading"
                                empty-text="هیچ محصولی وجود ندارد"
                                :data="products"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="id"
                                    label="شناسه"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="name"
                                    label="عنوان"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="price1"
                                    label="قیمت"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="price1"
                                    label="قیمت عادی"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="price2"
                                    label="قیمت ویژه"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip v-if="!findProduct(scope.row.id)" class="item" effect="dark"
                                                content="افزودن محصول به پکیج" placement="top-start">
                                        <el-button @click="addToPackage(scope.row.id)" size="mini" plain
                                                   round icon="fa fa-plus"></el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="findProduct(scope.row.id)" class="item" effect="dark"
                                                content="حذف محصول از پکیج" placement="top-start">
                                        <el-button @click="deleteFromPackage(scope.row.id,scope.$index)"
                                                   size="mini"
                                                   plain
                                                   round
                                                   type="danger"
                                                   icon="fa fa-minus"></el-button>
                                    </el-tooltip>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </div>
                <div class="row">
                    <br>
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
    </div>
</template>

<script>
    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                paginateProduct: [],
                loading: false,
                package_id: '',
                packageProduct: [],
                packs: [],
                products: [],
                filters: {
                    term: '',
                    page: 1,
                    category_ids: '',
                    brand_ids: [],
                }
            }
        },
        mounted(){
            this.package_id = this.$route.params.id;
            if (!this.auth) {
                this.$router.go(-1);
            }
            if (this.checkRole('admin') == -1) {
                this.$router.go(-1);
            }

            this.getPackage();
        },
        methods: {
            findProduct(id){
                var p = this.packs.products.find(function (product) {
                    return product.id == id;
                });
                return p == undefined ? false : true;
            },
            deleteFromPackage(product_id,index){
                var vm = this;
                vm.fullscreenLoading = true;
                axios.post('/v1/products/packages/delete-from-package', {
                    package_id: vm.package_id,
                    product_id: product_id
                }).then(function (response) {
                    vm.packs.products.splice(index,1);
                    vm.fullscreenLoading = false;
                }).catch(function (error) {
                    vm.fullscreenLoading = false;
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
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            addToPackage(product_id){
                var vm = this;
                vm.fullscreenLoading = true;
                axios.post('/v1/products/packages/add-to-package', {
                    package_id: vm.package_id,
                    product_id: product_id
                }).then(function (response) {
                    console.log(response.data);
                    vm.packs.products.push({id : product_id})
                    vm.fullscreenLoading = false;
                }).catch(function (error) {
                    console.log(error);
                    vm.fullscreenLoading = false;
                });
            },
            getFilterProduct(){
                var vm = this;
                var fi = vm.filters;
                axios.get(`/v1/products/filter/term=${fi.term}/page=${fi.page}/cats=${fi.category_ids}/brands=${fi.brand_ids.join('-')}`).then(function (response) {
                    vm.paginateProduct = response.data['products'];
                    vm.products = response.data['products'].data;
                })
            },
            getPackage(){
                var vm = this;
                vm.openFullScreen();
                axios.get(`/v1/products/get-package/${vm.package_id}`).then(function (response) {
                    vm.packs = response.data;
                    vm.getFilterProduct();
                    vm.openFullScreen().close();
                })
            },
            changePage(page){
                this.filters.page = page;
                this.getFilterProduct();
            },
            deleteProduct(product, index){
                var vm = this;
                this.$confirm('از حذف محصول اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    type: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/products/delete', {id: product.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.products.splice(index, 1);
                            vm.$notify({
                                title: 'حذف محصول',
                                message: 'محصول با موفقیت حذف شد.',
                                type: 'success',
                            });
                            vm.fullscreenLoading = false;
                        }
                    }).catch(function (errors) {
                        vm.fullscreenLoading = false;
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'حذف لغو شد.'
                    });

                });
            }
        }
    }
</script>

<style>

    .table-head {
        display: flex;
        flex-direction: row-reverse;
    }
</style>