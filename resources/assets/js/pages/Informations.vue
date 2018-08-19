<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">اطلاعیه ها
                        <div class="pull-left table-head">
                            <el-button size="mini" body="primary" @click="addInformation()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن اطلاعیه
                            </el-button>
                            &nbsp;
                            <el-input
                                    size="small"
                                    placeholder="جستجوی اطلاعیه"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.term"
                            >
                            </el-input>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                v-loading="loading"
                                empty-text="هیچ اطلاعیه ای وجود ندارد"
                                :data="informations"
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
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip class="item" effect="dark" content="ویرایش اطلاعیه" placement="top-start">
                                        <el-button @click="editInformation(scope.row)" body="primary" size="mini" plain
                                                   round icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" :content="scope.row.status ? 'مخفی کردن اطلاعیه':'نمایش اطلاعیه'" placement="top-start">
                                        <el-button @click="changeStatus(scope.$index)" body="primary" size="mini" plain
                                                   round type="warning" :icon="scope.row.status ? 'fa fa-eye':'fa fa-eye-slash'"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="حذف اطلاعیه" placement="top-start">
                                        <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="deleteInformation(scope.row,scope.$index)" body="danger" plain
                                                   size="mini"
                                                   type="danger"
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
                        <!--layout="prev, informationr, next"-->
                        <!--:total="paginateInformation.total"-->
                        <!--:information-size="paginateInformation.per_information"-->
                        <!--:current-information="filters.information"-->
                        <!--@current-change="changeInformation"-->
                        <!--&gt;-->
                        <!--</el-pagination>-->


                    </div>
                </div>
            </div>
        </div>

        <el-dialog :fullscreen="true" title="افزودن اطلاعیه" :visible.sync="addInformationForm.visible">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <el-form label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('title') }" label="عنوان اطلاعیه :">
                        <el-input
                                v-model="addInformationForm.data.title"
                                name="title"
                                data-vv-as="عنوان اطلاعیه"
                                v-validate="'required|min:5'"
                        ></el-input>
                        <span v-show="errors.has('title')">{{ errors.first('title') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('body') }" label="متن :">
                        <el-input
                                type="textarea"
                                name="body"
                                data-vv-as="متن اطلاعیه"
                                v-validate="'required|min:5'"
                                v-model="addInformationForm.data.body"
                        >
                        </el-input>
                        <span v-show="errors.has('body')">{{ errors.first('body') }}</span>
                    </el-form-item>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveInformation()" size="small" round body="success">ثبت اطلاعیه<i
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
                addInformationForm: {
                    visible: false,
                    title: '',
                    operation: 'add',
                    loading: false,
                    data: {
                        id: '',
                        title: '',
                        body: '',
                    }
                },
                fullscreenLoading: false,
                loading: false,
                informations: [],
                filters: {
                    term: '',
                    information: 1,
                    category_ids: '',
                    information_ids: [],
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
            this.getInformations();
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            addInformation(){
                this.addInformationForm.operation = 'add';
                this.addInformationForm.data = {
                    id: '',
                    title: '',
                    body: '',
                }
                this.addInformationForm.visible = true;
                this.errors.clear();
            },
            editInformation(information){
                this.addInformationForm.operation = 'edit'
                var info = this.addInformationForm.data;
                info.id = information.id;
                info.title = information.title;
                info.body = information.info;

                this.addInformationForm.visible = true;
            },
            getInformations(){
                var vm = this;
                vm.loading = true;
                axios.get(`/v1/informations/get-informations`).then(function (response) {
                    vm.informations = response.data;
                    vm.loading = false;
                });
            },
            saveInformation(){
                var vm = this;
                var datas = new FormData();
                var info = vm.addInformationForm.data;
                datas.append('title', info.title);
                datas.append('body', info.body);
                var form = {
                    name: info.name,
                    body: info.body,
                }
                if (vm.addInformationForm.operation == 'add') {
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true
                            axios.post('/v1/informations/store', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.informations.push(response.data);
                                    vm.$notify({
                                        title: 'ثبت اطلاعیه',
                                        message: 'اطلاعیه با موفقیت ثبت شد.',
                                        body: 'success',
                                    });
                                    vm.fullscreenLoading = false;
                                }
                            }).catch(function (errors) {
                                vm.fullscreenLoading = false;
                            })
                        }
                    });
                }
                if (vm.addInformationForm.operation == 'edit') {
                    datas.append('id', info.id);
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/informations/update', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: 'بروزرسانی اطلاعیه',
                                        message: 'اطلاعیه با موفقیت بروزرسانی شد.',
                                        body: 'success',
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
                axios.post(`/v1/informations/change-status`,{status:!vm.informations[index].status,id:vm.informations[index].id}).then(function (response) {
                    vm.informations[index].status = !vm.informations[index].status;
                    vm.loading = false;
                    console.log(response.data)
                }).catch(function (errors) {
                    vm.loading = false;
                });
            },
            deleteInformation(information, index){
                var vm = this;
                this.$confirm('از حذف اطلاعیه اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    body: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/informations/delete', {id: information.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.informations.splice(index, 1);
                            vm.$notify({
                                title: 'حذف اطلاعیه',
                                message: 'اطلاعیه با موفقیت حذف شد.',
                                body: 'success',
                            });
                            vm.fullscreenLoading = false;
                        }
                    }).catch(function (errors) {
                        vm.fullscreenLoading = false;
                    });
                }).catch(() => {
                    this.$message({
                        body: 'info',
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