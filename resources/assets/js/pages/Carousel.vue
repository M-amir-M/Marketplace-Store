<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">اسلایدها
                        <div class="pull-left table-head">
                            <el-button size="mini" type="primary" @click="addCarousel()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن اسلاید
                            </el-button>
                            &nbsp;
                            <el-input
                                    size="small"
                                    placeholder="جستجوی اسلاید"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.term"
                            >
                            </el-input>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                v-loading="loading"
                                empty-text="هیچ اسلایدی وجود ندارد"
                                :data="carousels"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="id"
                                    label="شناسه"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="title"
                                    label="عنوان"
                                    sortable>
                            </el-table-column>
                            <el-table-column
                                    prop="image"
                                    label="عکس">
                                <template slot-scope="scope">

                                </template>
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip class="item" effect="dark" content="ویرایش اسلاید" placement="top-start">
                                        <el-button @click="editCarousel(scope.row)" type="primary" size="mini" plain
                                                   round icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" :content="scope.row.status ? 'مخفی کردن اسلایدر':'نمایش اسلایدر'" placement="top-start">
                                        <el-button @click="changeStatus(scope.$index)" body="primary" size="mini" plain
                                                   round type="warning" :icon="scope.row.status ? 'fa fa-eye':'fa fa-eye-slash'"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="حذف اسلاید" placement="top-start">
                                        <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="deleteCarousel(scope.row,scope.$index)" type="danger" plain
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
                        <!--:total="paginateCarousel.total"-->
                        <!--:page-size="paginateCarousel.per_page"-->
                        <!--:current-page="filters.page"-->
                        <!--@current-change="changePage"-->
                        <!--&gt;-->
                        <!--</el-pagination>-->


                    </div>
                </div>
            </div>
        </div>

        <el-dialog :fullscreen="true" title="افزودن اسلاید" :visible.sync="addCarouselForm.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('name') }" label="نام اسلاید :">
                        <el-input
                                v-model="addCarouselForm.data.name"
                                name="name"
                                data-vv-as="نام اسلاید"
                                v-validate="'required|min:5'"
                        ></el-input>
                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('image') }" label="عکس  :">
                        <input
                                name="image"
                                data-vv-as="عکس "
                                v-validate="'required|mimes:image/jpeg,image/jpg,image/png|size:4096'"
                                class="input-sm"
                                id="carousel_image"
                                type="file"
                        >
                        <span v-show="errors.has('image')">{{ errors.first('image') }}</span>

                    </el-form-item>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveCarousel()" size="small" round type="success">ثبت اسلاید<i
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
                addCarouselForm: {
                    visible: false,
                    title: '',
                    operation: 'add',
                    loading: false,
                    data: {
                        id: '',
                        name: '',
                    }
                },
                fullscreenLoading: false,
                loading: false,
                carousels: [],
                filters: {
                    term: '',
                    page: 1,
                    category_ids: '',
                    carousel_ids: [],
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
            this.getCarousels();
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            addCarousel(){
                this.addCarouselForm.operation = 'add';
                this.addCarouselForm.data = {
                    id: '',
                    name: '',
                }
                this.addCarouselForm.visible = true;
                this.errors.clear();
            },
            editCarousel(carousel){
                this.addCarouselForm.operation = 'edit'
                var info = this.addCarouselForm.data;
                info.id = carousel.id;
                info.name = carousel.title;

                this.addCarouselForm.visible = true;
            },
            getCarousels(){
                var vm = this;
                vm.loading = true;
                axios.get(`/v1/carousels/get-carousels`).then(function (response) {
                    vm.carousels = response.data;
                    vm.loading = false;
                })
            },
            saveCarousel(){
                var vm = this;
                var datas = new FormData();
                var info = vm.addCarouselForm.data;
                datas.append('name', info.name);
                datas.append('image', document.getElementById('carousel_image').files[0]);

                var form = {
                    name: info.name,
                }

                if (datas.get('image') == "undefined" && vm.addCarouselForm.operation == 'add') {
                    form = {
                        name: info.name,
                        image: document.getElementById('carousel_image').files[0]
                    }
                }
                if (vm.addCarouselForm.operation == 'add') {
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true
                            axios.post('/v1/carousels/store', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.carousels.push(response.data);
                                    vm.$notify({
                                        title: 'ثبت اسلاید',
                                        message: 'اسلاید با موفقیت ثبت شد.',
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
                if (vm.addCarouselForm.operation == 'edit') {
                    datas.append('id', info.id);
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/carousels/update', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: 'بروزرسانی اسلاید',
                                        message: 'اسلاید با موفقیت بروزرسانی شد.',
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
            changeStatus(index){
                var vm = this;
                vm.loading = true;
                axios.post(`/v1/carousels/change-status`,{status:!vm.carousels[index].status,id:vm.carousels[index].id}).then(function (response) {
                    vm.carousels[index].status = !vm.carousels[index].status;
                    vm.loading = false;
                }).catch(function (errors) {
                    vm.loading = false;
                });
            },
            changePage(page){
                this.filters.page = page;
                this.getCarousels();
            },
            deleteCarousel(carousel, index){
                var vm = this;
                this.$confirm('از حذف اسلاید اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    type: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/carousels/delete', {id: carousel.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.carousels.splice(index, 1);
                            vm.$notify({
                                title: 'حذف اسلاید',
                                message: 'اسلاید با موفقیت حذف شد.',
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