<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default"  v-loading.body="loading">
                    <div class="panel-heading">دسته بندی محصولات
                        <div class="pull-left">
                            <el-button size="mini" type="primary" @click="operationFunction('add-m')"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن دسته مادر
                            </el-button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-tree
                                :data="categories"
                                empty-text="هیچ دسته بندی وجود ندارد"
                                :props="defaultProps"
                                show-checkbox
                                node-key="id"
                                :expand-on-click-node="true"
                                :check-strictly="true"
                                ref="cat"
                                @check-change="handleCheckChange">
                        </el-tree>
                    </div>
                </div>
            </div>
        </div>

        <el-dialog
                title=""
                :visible.sync="operationDialogVisible"
                width="30%"
                center
                :before-close="handleClose">

            <h3 style="text-align: center">شما دسته
                <el-tag size="small">{{category.name}}</el-tag>
                را انتخاب کردی.
            </h3>
            <h3 style="text-align: center">میخوای چیکار کنی؟ <span class="fa fa-smile-o"></span></h3>


            <span slot="footer" class="dialog-footer">
    <el-button type="primary" @click="operationFunction('edit')">ویرایش دسته</el-button>
    <el-button type="success" @click="operationFunction('add')">افزودن زیر دسته</el-button>
    <el-button type="danger" @click="deleteCategory()">حذف دسته</el-button>
  </span>
        </el-dialog>

        <el-dialog
                :fullscreen="true"
                :title="categoryForm.title"
                :visible.sync="categoryForm.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form status-icon label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('name') }" label="عنوان :">
                        <el-input name="name" data-vv-as="عنوان دسته" v-model="categoryForm.data.name"
                                  v-validate="'required|min:3'"></el-input>
                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                    </el-form-item>

                    <el-form-item>
                        <div class="form-buttons">
                            <el-button @click="saveCategory()" size="small" round type="success">{{categoryForm.submit}}<i
                                    class="el-icon-arrow-left el-icon-right"></i></el-button>
                        </div>
                    </el-form-item>

                </el-form>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    export default{
        data(){
            return {
                auth: window.Laravel.auth,
                loading: false,
                operationDialogVisible: false,
                categoryForm: {
                    visible: false,
                    title: '',
                    submit: '',
                    operation: '',
                    loading: false,
                    data: {
                        id: '',
                        name: '',
                        parent_id: '',
                    }
                },
                defaultProps: {
                    children: 'child',
                    label: 'name'
                },
                categories: [],
                category: {},
            }
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            saveCategory(){
                var vm = this;
                var form = {
                    id : vm.categoryForm.data.id,
                    name : vm.categoryForm.data.name
                }
                this.$validator.validateAll(form).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;
                        if (vm.categoryForm.operation == 'add' || vm.categoryForm.operation == 'add-m') {
                            vm.categoryForm.data.parent_id = vm.categoryForm.operation == 'add' ? vm.category.id : 0;
                            axios.post('/v1/categories/store', vm.categoryForm.data).then(function (response) {
                                if (response.status == 200) {
                                    vm.categories = response.data;
                                    vm.categoryForm.data.name = '';
                                    vm.$notify({
                                        title: 'ایجاد دسته',
                                        message: 'ایجاد دسته با موفقیت انجام شد.',
                                        type: 'success',
                                    });
                                    vm.fullscreenLoading = false
                                }
                            }).catch(function (errors) {
                                vm.fullscreenLoading = false;
                            })
                        }
                        if (vm.categoryForm.operation == 'edit') {
                            vm.categoryForm.data.id = vm.category.id;
                            vm.categoryForm.data.parent_id = vm.category.parent_id;
                            vm.fullscreenLoading = true;
                            axios.post('/v1/categories/update', vm.categoryForm.data).then(function (response) {
                                if (response.status == 200) {
                                    vm.categories = response.data;
                                    vm.$notify({
                                        title: 'ویرایش دسته',
                                        message: 'ویرایش دسته با موفقیت انجام شد.',
                                        type: 'success',
                                    });
                                    vm.fullscreenLoading = false;
                                }
                            }).catch(error => {
                                vm.fullscreenLoading = false;
                            });
                        }
                    }
                });
            },
            getCategories(){
                var vm = this;
                vm.loading = true
                axios.get('/v1/categories/get-categories').then(function (response) {
                    vm.categories = response.data
                    vm.loading = false;
                })
            },
            deleteCategory(){
                var vm = this;
                this.$confirm('از حذف دسته اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    type: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/categories/delete', {id: vm.category.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.$notify({
                                title: 'حذف دسته',
                                message: 'دسته با موفقیت حذف شد.',
                                type: 'success',
                            });
                            vm.categories = response.data;
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
            },
            handleClose(done) {
                this.resetChecked();
                done();
            },
            operationFunction(operation){
                if (operation == 'edit') {
                    this.categoryForm.title = `ویرایش دسته ${this.category.name}`;
                    this.categoryForm.submit = "بروز رسانی";
                    this.categoryForm.operation = "edit";
                    this.categoryForm.data.name = this.category.name;
                    this.categoryForm.visible = true;
                }
                if (operation == 'add') {
                    this.categoryForm.title = `افزودن زیردسته به  ${this.category.name}`;
                    this.categoryForm.submit = "ایجاد زیر دسته";
                    this.categoryForm.operation = "add";
                    this.categoryForm.data.name = '';
                    this.categoryForm.visible = true;
                }
                if (operation == 'add-m') {
                    this.categoryForm.title = `افزودن دسته مادر`;
                    this.categoryForm.submit = "ایجاد دسته";
                    this.categoryForm.operation = "add-m";
                    this.categoryForm.data.name = '';
                    this.categoryForm.visible = true;
                }
            },
            handleCheckChange(data, checked, indeterminate) {
                if (checked) {
                    this.category = data
                    console.log(data);
                    this.operationDialogVisible = true;
                }
            },
            resetChecked() {
                this.$refs.cat.setCheckedKeys([]);
            }

        },
        mounted(){
            if(!this.auth){
                this.$router.go(-1);
            }
            if(this.checkRole('admin')==-1){
                this.$router.go(-1);
            }
            this.getCategories();
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
</style>
