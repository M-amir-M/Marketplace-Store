<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">محصولات
                        <div class="pull-left table-head">
                            <el-button size="mini" type="primary" @click="addProduct()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن محصول
                            </el-button>
                            &nbsp;
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
                                    prop="price2"
                                    label="قیمت عادی"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="price3"
                                    label="قیمت ویژه"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip class="item" effect="dark" content="جزئیات محصول" placement="top-start">
                                        <router-link
                                                :to="{name: 'product.detail',params:{title:replaceAll(scope.row.name,' ', '-'),id:scope.row.id,type:'product'}}">
                                            <el-button type="info" size="mini" plain
                                                       round icon="fa fa-info"></el-button>
                                        </router-link>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="ویرایش محصول" placement="top-start">
                                        <el-button @click="editProduct(scope.row)" type="primary" size="mini" plain
                                                   round icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="checkRole('admin') != -1" class="item" effect="dark"
                                                :content="scope.row.status ? 'عدم نمایش محصول':'تایید محصول'"
                                                placement="top-start">
                                        <el-button @click="changeStatus(scope.$index)" body="primary" size="mini" plain
                                                   round type="warning"
                                                   :icon="scope.row.status ? 'fa fa-eye':'fa fa-eye-slash'"></el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="checkRole('brand') != -1" class="item" effect="dark"
                                                :content="scope.row.status ? 'محصول تایید شده':'محصول تایید نشده'"
                                                placement="top-start">
                                        <el-button body="primary" size="mini" plain
                                                   round type="warning"
                                                   :icon="scope.row.status ? 'fa fa-eye':'fa fa-eye-slash'"></el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="checkRole('admin') != -1" class="item" effect="dark"
                                                content="حذف محصول" placement="top-start">
                                        <el-button @click="deleteProduct(scope.row,scope.$index)" type="danger" plain
                                                   size="mini"
                                                   round icon="fa fa-trash-o"></el-button>
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
        <el-dialog :fullscreen="true" title="افزودن محصول" :visible.sync="addProductForm.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('name') }" label="نام محصول :">
                        <el-input
                                v-model="addProductForm.data.name"
                                name="name"
                                data-vv-as="نام محصول"
                                v-validate="'required|min:5'"
                        ></el-input>
                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                    </el-form-item>
                    <el-form-item v-if="checkRole('admin') != -1" :class="{'input-empty': errors.has('brand_id') }"
                                  label="تولید کننده :">
                        <el-select v-model="addProductForm.data.brand_id"
                                   name="brand_id"
                                   data-vv-as="تولید کننده"
                                   v-validate="'required'"
                                   placeholder="تولید کننده"
                                   filterable
                        >
                            <el-option v-for="brand in brands" :key="brand.id" :label="brand.name"
                                       :value="brand.id"></el-option>
                        </el-select>
                        <span v-show="errors.has('brand_id')">{{ errors.first('brand_id') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('category_id') }" label="دسته بندی :">
                        <div>
                            <input type="hidden"
                                   name="category_id"
                                   data-vv-as="دسته"
                                   v-validate="'required'"
                                   v-model="addProductForm.data.category_id">

                            <el-button

                                    @click="addProductForm.category_dialog = true"> انتخاب دسته بندی
                            </el-button>
                            <el-tag v-if="category != ''">
                            <span v-for="(path,index) in path_category">
                                <span v-if="index !=0" class="fa fa-chevron-left"></span>{{path_category[path_category.length - index-1].name}}
                            </span>
                            </el-tag>
                        </div>

                        <span v-show="errors.has('category_id')">{{ errors.first('category_id') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('quantity') }" label="تعداد :">
                        <el-input
                                name="quantity"
                                data-vv-as="تعداد محصول"
                                v-validate="'required|numeric'"
                                v-model="addProductForm.data.quantity"></el-input>
                        <span v-show="errors.has('quantity')">{{ errors.first('quantity') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('amount') }" label="مقدار :">
                        <el-input
                                name="amount"
                                data-vv-as="مقدار محصول"
                                v-validate="'required'"
                                placeholder="600 گرمی"
                                v-model="addProductForm.data.amount"></el-input>
                        <span v-show="errors.has('amount')">{{ errors.first('amount') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('price1') }" label="قیمت محصول:">
                        <el-input
                                name="price1"
                                data-vv-as="قیمت محصول"
                                v-validate="'required'"
                                v-model="addProductForm.data.price1"></el-input>
                        <span v-show="errors.has('price1')">{{ errors.first('price1') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('price2') }" label="قیمت عادی:">
                        <el-input
                                name="price2"
                                data-vv-as="قیمت عادی"
                                v-validate="'required'"
                                v-model="addProductForm.data.price2"></el-input>
                        <span v-show="errors.has('price2')">{{ errors.first('price2') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('price3') }" label="قیمت ویژه:">
                        <el-input
                                name="price3"
                                data-vv-as="قیمت همکار"
                                v-validate="'required'"
                                v-model="addProductForm.data.price3"></el-input>
                        <span v-show="errors.has('price3')">{{ errors.first('price3') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('min_order') }" label="حداقل سفارش :">
                        <el-input
                                name="min_order"
                                data-vv-as="حداقل سفارش"
                                v-validate="'required'"
                                v-model="addProductForm.data.min_order"></el-input>
                        <span v-show="errors.has('min_order')">{{ errors.first('min_order') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('description') }" label="توضیحات :">
                        <el-input
                                name="description"
                                data-vv-as="توضیحات محصول"
                                v-validate="'max:80'"
                                type="textarea" v-model="addProductForm.data.description"></el-input>
                        <span v-show="errors.has('description')">{{ errors.first('description') }}</span>

                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('image') }" label="عکس محصول :">
                        <input
                                name="image"
                                data-vv-as="عکس محصول"
                                v-validate="'required|mimes:image/jpeg,image/jpg,image/png|size:4096'"
                                class="input-sm"
                                id="product_image"
                                type="file"
                        >
                        <span v-show="errors.has('image')">{{ errors.first('image') }}</span>

                    </el-form-item>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveProduct()" size="small"
                                       round type="success">{{addProductForm.title}}<i
                                    class="el-icon-arrow-left el-icon-right"></i></el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>

        <el-dialog
                title=""
                :visible.sync="addProductForm.category_dialog"
                width="30%"
                center>
            <el-tree
                    :data="categories"
                    :props="defaultProps"
                    show-checkbox
                    node-key="id"
                    :expand-on-click-node="true"
                    :check-strictly="true"
                    ref="cat"
                    @check-change="handleCheckChange">
            </el-tree>
        </el-dialog>

    </div>
</template>

<script>
    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                addProductForm: {
                    page: 1,
                    mode: '',
                    visible: false,
                    category_dialog: false,
                    title: '',
                    operation: 'add',
                    loading: false,
                    data: {
                        id: '',
                        name: '',
                        brand_id: '',
                        category_id: '',
                        quantity: '',
                        unit: '0',
                        amount: '',
                        price1: '',
                        price2: '',
                        price3: '',
                        min_order: '',
                        image: false,
                        description: '',
                    }
                },
                brands: [],
                paginateProduct: [],
                categories: [],
                category: '',
                loading: false,
                path_category: [],
                products: [],
                defaultProps: {
                    children: 'child',
                    label: 'name'
                },
                filters: {
                    term: '',
                    page: 1,
                    category_ids: '',
                    brand_ids: [],
                }
            }
        },
        mounted(){
            if (!this.auth) {
                this.$router.go(-1);
            }
            if (this.checkRole('admin') == -1 && this.checkRole('brand') == -1) {
                this.$router.go(-1);
            }
            this.getFilterProduct();
        },
        methods: {
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            getFilterProduct(){
                var vm = this;
                if (this.checkRole('brand') != -1)
                    vm.filters.brand_ids= [window.Laravel.user.brand_id];
                var fi = vm.filters;
                vm.loading = true
                axios.get(`/v1/products/filter/term=${fi.term}/page=${fi.page}/cats=${fi.category_ids}/brands=${fi.brand_ids.join('-')}`).then(function (response) {
                    vm.paginateProduct = response.data['products']
                    vm.products = response.data['products'].data
                    vm.loading = false;
                })
            },
            addProduct(){
                this.addProductForm.operation = 'add';
                this.addProductForm.title = ' افزودن محصول';
                this.addProductForm.data = {
                    id: '',
                    name: '',
                    brand_id: '',
                    category_id: '',
                    quantity: '',
                    amount: '',
                    price1: '',
                    price2: '',
                    price3: '',
                    min_order: '',
                    image: false,
                    description: '',
                }
                this.getBrands();
                this.getCategories();
                this.addProductForm.visible = true;
                this.category = '';
                this.errors.clear();
            },
            editProduct(product){
                this.addProductForm.operation = 'edit';
                this.addProductForm.title = ' بروزرسانی محصول';

                this.getBrands();
                this.getCategories();
                this.getProductCategory(product.category_id);

                var info = this.addProductForm.data;
                info.id = product.id;
                info.name = product.name;
                info.brand_id = product.brand_id;
                info.category_id = product.category_id;
                info.quantity = product.quantity;
                info.amount = product.amount;
                info.price1 = product.price1;
                info.price2 = product.price2;
                info.price3 = product.price3;
                info.min_order = product.min_order;
                info.description = product.description;

                this.addProductForm.visible = true;
            },
            getBrands(){
                var vm = this;
                axios.get('/v1/brands/get-brands').then(function (response) {
                    vm.brands = response.data;
                });
            },
            getProducts(){
                var vm = this;
                vm.addProductForm.loading = true
                axios.get(`/v1/products/get-products/${vm.page}`).then(function (response) {
                    vm.products = response.data;
                    vm.addProductForm.loading = false
                })
            },
            getCategories(){
                var vm = this;
                axios.get('/v1/categories/get-categories').then(function (response) {
                    vm.categories = response.data
                })
            },
            getProductCategory(id){
                var vm = this;
                axios.get(`/v1/products/get-category/${id}`).then(function (response) {
                    vm.category = response.data;
                    vm.path_category = [];
                    for (var i = vm.category.length; i > 0; i--) {
                        vm.path_category.push({id: vm.category[i - 1][0], name: vm.category[i - 1][1]})

                    }
                })
            },
            changeStatus(index){
                var vm = this;
                if (this.checkRole('admin') != -1){
                    vm.loading = true;
                    axios.post(`/v1/products/change-status`, {
                        status: !vm.products[index].status,
                        id: vm.products[index].id
                    }).then(function (response) {
                        if (response.status == 200) {
                            vm.products[index].status = !vm.products[index].status;
                        }
                        vm.loading = false;
                    }).catch(function (errors) {
                        vm.loading = false;
                    });
                }
            },
            saveProduct(){
                var vm = this;
                var datas = new FormData();
                var info = vm.addProductForm.data;
                datas.append('name', info.name);
                datas.append('brand_id', info.brand_id);
                datas.append('category_id', info.category_id);
                datas.append('quantity', info.quantity);
                datas.append('amount', info.amount);
                datas.append('price1', info.price1);
                datas.append('price2', info.price2);
                datas.append('price3', info.price3);
                datas.append('min_order', info.min_order);
                datas.append('description', info.description);
                datas.append('image', document.getElementById('product_image').files[0]);


                var form = {
                    name: info.name,
                    brand_id: info.brand_id,
                    category_id: info.category_id,
                    quantity: info.quantity,
                    amount: info.amount,
                    price1: info.price1,
                    price2: info.price2,
                    price3: info.price3,
                    min_order: info.min_order,
                    description: info.description,
                }

                if (datas.get('image') == "undefined" && vm.addProductForm.operation == 'add') {
                    form = {
                        name: info.name,
                        brand_id: info.brand_id,
                        category_id: info.category_id,
                        quantity: info.quantity,
                        amount: info.amount,
                        price1: info.price1,
                        price2: info.price2,
                        price3: info.price3,
                        min_order: info.min_order,
                        description: info.description,
                        image: document.getElementById('product_image').files[0]
                    }
                }
                if (vm.addProductForm.operation == 'add') {
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/products/store', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.products.push(response.data);
                                    vm.$notify({
                                        title: 'ثبت محصول',
                                        message: 'محصول با موفقیت ثبت شد.',
                                        type: 'success',
                                    });
                                    vm.fullscreenLoading = false;
                                }
                            }).catch(function (errors) {
                                vm.fullscreenLoading = false;
                            })
                        }
                    });
                }
                if (vm.addProductForm.operation == 'edit') {
                    datas.append('id', info.id);
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/products/update', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: 'بروزرسانی محصول',
                                        message: 'محصول با موفقیت بروزرسانی شد.',
                                        type: 'success',
                                    });
                                    vm.fullscreenLoading = false;
                                }
                            }).catch(function (errors) {
                                vm.fullscreenLoading = false;
                            })
                        }
                    });
                }
            },
            handleCheckChange(data, checked, indeterminate) {
                if (checked) {
                    var arr = [];
                    arr.push(data.id);
                    this.$refs.cat.setCheckedKeys(arr);
                    this.category = data;
                    this.addProductForm.data.category_id = data.id;
                    this.addProductForm.category_dialog = false;
                    this.path_category = this.getPathCat(data);
                    this.$refs.cat.setCheckedKeys(arr);
                }
            },
            getPathCat(node){
                node = [node]
                var parent_id = node[0].parent_id
                var path = [];
                path.push(node[0]);
                while (parent_id != 0) {
                    var arr = [];
                    arr.push(node[0].parent_id);
                    this.$refs.cat.setCheckedKeys(arr);
                    node = this.$refs.cat.getCheckedNodes();
                    parent_id = node[0].parent_id
                    path.push(node[0]);
                }
                return path;
            },
            resetChecked() {
                this.$refs.cat.setCheckedKeys([]);
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
    .el-tree-node__children {
        padding-right: 36px;
        padding-left: 0 !important;
    }

    .el-tree-node__expand-icon {
        border-left-width: 0;
        border-right-color: #b4bccc;
        border-right-width: 6px;
    }

    .caret-wrapper {
        float: left;
    }

    .table-head {
        display: flex;
        flex-direction: row-reverse;
    }
</style>