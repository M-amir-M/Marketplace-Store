<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">پکیج ها
                        <div class="pull-left table-head">
                            <el-button size="mini" type="primary" @click="addPackage()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن پکیج
                            </el-button>
                            &nbsp;
                            <el-input
                                    size="small"
                                    placeholder="جستجوی پکیج"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.term"
                                    @input="getFilterPackage()"
                            >
                            </el-input>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                v-loading="loading"
                                empty-text="هیچ پکیجی وجود ندارد"
                                :data="packs"
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
                                    prop="discount"
                                    label="تخفیف(درصد)"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip class="item" effect="dark" content="جزئیات پکیج" placement="top-start">
                                        <router-link :to="{name: 'package',params:{id:scope.row.id}}">
                                            <el-button  type="info" size="mini" plain
                                                        round icon="fa fa-info"></el-button>
                                        </router-link>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="ویرایش پکیج" placement="top-start">
                                        <el-button @click="editPackage(scope.row)" type="primary" size="mini" plain
                                                   round icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="حذف پکیج" placement="top-start">
                                        <el-button @click="deletePackage(scope.row,scope.$index)" type="danger" plain size="mini"
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
                                :total="paginatePackage.total"
                                :page-size="paginatePackage.per_page"
                                :current-page="filters.page"
                                @current-change="changePage"
                        >
                        </el-pagination>


                    </div>
                </div>
            </div>
        </div>

        <el-dialog :fullscreen="true" title="افزودن پکیج" :visible.sync="addPackageForm.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('name') }" label="نام پکیج :">
                        <el-input
                                v-model="addPackageForm.data.name"
                                name="name"
                                data-vv-as="نام پکیج"
                                v-validate="'required'"
                        ></el-input>
                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('discount') }" label="تخفیف (درصد):">
                        <el-input
                                v-model="addPackageForm.data.discount"
                                name="discount"
                                data-vv-as="تخفیف"
                                v-validate="'required'"
                        ></el-input>
                        <span v-show="errors.has('discount')">{{ errors.first('discount') }}</span>
                    </el-form-item>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="savePackage()" size="small" round type="success">ثبت کالا<i
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
                fullscreenLoading:false,
                addPackageForm: {
                    visible: false,
                    title: '',
                    operation: 'add',
                    loading: false,
                    data: {
                        id: '',
                        name: '',
                        discount: '',
                    }
                },
                paginatePackage: [],
                categories: [],
                loading: false,
                packs: [],
                filters: {
                    term: '',
                    page: 1,
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
            this.getFilterPackage();
        },
        methods: {
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            getFilterPackage(){
                var vm = this;
                var fi = vm.filters;
                vm.loading = true
                axios.get(`/v1/products/packages/filter/term=${fi.term}/page=${fi.page}`).then(function (response) {
                    vm.paginatePackage = response.data['packages'];
                    vm.packs = response.data['packages'].data
                    vm.loading = false;
                });
            },
            addPackage(){
                this.addPackageForm.operation = 'add';
                this.addPackageForm.data = {
                    id: '',
                    name: '',
                    discount: '',
                }
                this.addPackageForm.visible = true;
                this.category = '';
                this.errors.clear();
            },
            editPackage(pack){
                this.addPackageForm.operation = 'edit';

                var info = this.addPackageForm.data;
                info.id = pack.id;
                info.name = pack.name;
                info.discount = pack.discount;

                this.addPackageForm.visible = true;
            },
            savePackage(){
                var vm = this;
                var datas = new FormData();
                var info = vm.addPackageForm.data;
                datas.append('name', info.name);
                datas.append('discount', info.discount);


                var form = {
                    name: info.name,
                    discount: info.discount,
                }

                if (vm.addPackageForm.operation == 'add') {
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/products/packages/store', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.packs.push(response.data);
                                    vm.$notify({
                                        title: 'ثبت پکیج',
                                        message: 'پکیج با موفقیت ثبت شد.',
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
                if (vm.addPackageForm.operation == 'edit') {
                    datas.append('id', info.id);
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/products/packages/update', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: 'بروزرسانی پکیج',
                                        message: 'پکیج با موفقیت بروزرسانی شد.',
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
                this.getFilterPackage();
            },
            deletePackage(pack,index){
                var vm = this;
                this.$confirm('از حذف پکیج اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    type: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/products/packages/delete',{id: pack.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.packs.splice(index,1);
                            vm.$notify({
                                title: 'حذف پکیج',
                                message: 'پکیج با موفقیت حذف شد.',
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