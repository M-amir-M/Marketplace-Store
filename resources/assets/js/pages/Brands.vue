<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">برندها
                        <div class="pull-left table-head">
                            <el-button size="mini" type="primary" @click="addBrand()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن برند
                            </el-button>
                            &nbsp;
                            <el-input
                                    size="small"
                                    placeholder="جستجوی برند"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.term"
                            >
                            </el-input>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                v-loading="loading"
                                empty-text="هیچ برندی وجود ندارد"
                                :data="brands"
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
                                    prop="type_name"
                                    label="نوع">
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip class="item" effect="dark" content="ویرایش برند" placement="top-start">
                                        <el-button @click="editBrand(scope.row)" type="primary" size="mini" plain
                                                   round icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="حذف برند" placement="top-start">
                                        <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="deleteBrand(scope.row,scope.$index)" type="danger" plain
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
                        <!--<el-pagination-->
                        <!--background-->
                        <!--layout="prev, pager, next"-->
                        <!--:total="paginateBrand.total"-->
                        <!--:page-size="paginateBrand.per_page"-->
                        <!--:current-page="filters.page"-->
                        <!--@current-change="changePage"-->
                        <!--&gt;-->
                        <!--</el-pagination>-->


                    </div>
                </div>
            </div>
        </div>

        <el-dialog :fullscreen="true" title="افزودن برند" :visible.sync="addBrandForm.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('name') }" label="نام برند :">
                        <el-input
                                v-model="addBrandForm.data.name"
                                name="name"
                                data-vv-as="نام برند"
                                v-validate="'required'"
                        ></el-input>
                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('type') }" label="نوع :">
                        <el-select v-model="addBrandForm.data.type"
                                   name="type"
                                   data-vv-as="نوع"
                                   v-validate="'required'"
                                   paceholder="نوع"
                        >
                            <el-option label="عمده فروش (عرضه کننده)" value="market"></el-option>
                            <el-option label="تولیدی" value="factory"></el-option>
                        </el-select>
                        <span v-show="errors.has('type')">{{ errors.first('type') }}</span>
                    </el-form-item>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveBrand()" size="small" round type="success">ثبت برند<i
                                    class="el-icon-arrow-left el-icon-right"></i></el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                addBrandForm: {
                    visible: false,
                    title: '',
                    operation: 'add',
                    loading: false,
                    data: {
                        id: '',
                        name: '',
                        type: '',
                    }
                },
                fullscreenLoading: false,
                loading: false,
                brands: [],
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
            if (this.checkRole('admin') == -1) {
                this.$router.go(-1);
            }
            this.getBrands();
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            addBrand(){
                this.addBrandForm.operation = 'add';
                this.addBrandForm.data = {
                    id: '',
                    name: '',
                    type: '',
                }
                this.addBrandForm.visible = true;
                this.errors.clear();
            },
            editBrand(brand){
                this.addBrandForm.operation = 'edit'
                var info = this.addBrandForm.data;
                info.id = brand.id;
                info.name = brand.name;
                info.type = brand.type;

                this.addBrandForm.visible = true;
            },
            getBrands(){
                var vm = this;
                vm.loading = true;
                axios.get(`/v1/brands/get-brands`).then(function (response) {
                    vm.brands = response.data;
                    vm.loading = false;
                })
            },
            saveBrand(){
                var vm = this;
                var datas = new FormData();
                var info = vm.addBrandForm.data;
                datas.append('name', info.name);
                datas.append('type', info.type);
                var form = {
                    name: info.name,
                    type: info.type,
                }
                if (vm.addBrandForm.operation == 'add') {
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true
                            axios.post('/v1/brands/store', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.brands.push(response.data);
                                    vm.$notify({
                                        title: 'ثبت برند',
                                        message: 'برند با موفقیت ثبت شد.',
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
                if (vm.addBrandForm.operation == 'edit') {
                    datas.append('id', info.id);
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/brands/update', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: 'بروزرسانی برند',
                                        message: 'برند با موفقیت بروزرسانی شد.',
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
            changePage(page){
                this.filters.page = page;
                this.getBrands();
            },
            deleteBrand(brand, index){
                var vm = this;
                this.$confirm('از حذف برند اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    type: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/brands/delete', {id: brand.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.brands.splice(index, 1);
                            vm.$notify({
                                title: 'حذف برند',
                                message: 'برند با موفقیت حذف شد.',
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