<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default" v-loading.body="loading">
                    <div class="panel-heading">کاربران
                        <div class="pull-left table-head">
                            <el-button size="mini" type="primary" @click="addUser()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن کاربر
                            </el-button>
                            &nbsp
                            <el-input
                                    size="small"
                                    placeholder="جستجوی کاربر"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.name"
                                    @input="getFilterUser()"
                            >
                            </el-input>
                            &nbsp
                            <el-select
                                    size="small"
                                    v-model="filters.role"
                                    placeholder="نوع کاربر"
                                    @change="getFilterUser()"
                            >
                                <el-option label="همه" value=""></el-option>
                                <el-option label="مدیر سیستم" value="admin"></el-option>
                                <el-option label="تولید کننده" value="brand"></el-option>
                                <el-option label="مصرف کننده" value="p_customer"></el-option>
                                <el-option label="عمده فروش (عرضه کننده)" value="s_customer"></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                :data="users"
                                empty-text="هیچ کاربری وجود ندارد"
                                border
                                style="width: 100%">
                            <el-table-column
                                    prop="code"
                                    label="کد مشتری">
                            </el-table-column>
                            <el-table-column
                                    prop="name"
                                    label="نام">
                            </el-table-column>
                            <el-table-column
                                    prop="mobile"
                                    label="همراه ">
                            </el-table-column>
                            <el-table-column
                                    label="عملیات">
                                <template slot-scope="scope">
                                    <el-tooltip class="item" effect="dark" content="ویرایش کاربر" placement="top-start">
                                        <el-button @click="editUser(scope.row)" type="primary" size="mini" plain round
                                                   icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="سفارشات کاربر" placement="top-start">
                                        <router-link
                                                :to="{name: 'user-orders',params : {id: scope.row.id}}">
                                            <el-button type="warning" size="mini" plain
                                                       round icon="fa fa-shopping-cart"></el-button>
                                        </router-link>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="حذف کاربر" placement="top-start">
                                        <el-button @click="deleteUser(scope.row,scope.$index)" type="danger" plain
                                                   size="mini" round icon="fa fa-trash-o"></el-button>
                                    </el-tooltip>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <br>
            <div class="block text-center">
                <el-pagination
                        background
                        layout="prev, pager, next"
                        :total="paginateUser.total"
                        :page-size="paginateUser.per_page"
                        :current-page="filters.page"
                        @current-change="changePage"
                >
                </el-pagination>


            </div>
        </div>

        <el-dialog :fullscreen="true" :title="addUserForm.title" :visible.sync="addUserForm.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form v-show="addUserForm.page == 1" label-position="top" size="small">

                    <el-form-item :class="{'input-empty': errors.has('name') }" label="* نام و نام خانوادگی :">
                        <el-input name="name" data-vv-as="نام و نام خانوادگی" v-validate="'required|min:5'"
                                  v-model="addUserForm.data.name"></el-input>
                        <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('type') }" label="* نوع کاربر :">
                        <el-select name="type"
                                   data-vv-as="نوع کاربر"
                                   v-validate="'required'"
                                   v-model="addUserForm.data.type"
                                   placeholder="نوع کاربر">
                            <el-option label="مدیر سیستم" value="admin"></el-option>
                            <el-option label="تولید کننده" value="brand"></el-option>
                            <el-option label="مصرف کننده" value="p_customer"></el-option>
                            <el-option label="عمده فروش (عرضه کننده)" value="s_customer"></el-option>
                        </el-select>
                        <span v-show="errors.has('type')">{{ errors.first('type') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('email') }" label="ایمیل :">
                        <el-input name="email" data-vv-as="ایمیل" v-validate="'email'"
                                  v-model="addUserForm.data.email"></el-input>
                        <span v-show="errors.has('email')">{{ errors.first('email') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('phone') }" label="تلفن :">
                        <el-input name="phone" data-vv-as="تلفن" v-validate="'numeric|min:5|max:15'"
                                  v-model="addUserForm.data.phone"></el-input>
                        <span v-show="errors.has('phone')">{{ errors.first('phone') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('mobile') }" label="* همراه :">
                        <el-input name="mobile" data-vv-as="شماره همراه" v-validate="'required|numeric|min:10|max:15'"
                                  v-model="addUserForm.data.mobile"></el-input>
                        <span v-show="errors.has('mobile')">{{ errors.first('mobile') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('password') }" label="* رمز عبور :">
                        <el-input name="password" data-vv-as="رمز عبور" v-validate="'required|min:6'"
                                  v-model="addUserForm.data.password" type="password"></el-input>
                        <span v-show="errors.has('password')">{{ errors.first('password') }}</span>
                    </el-form-item>
                    <br>
                    <el-form-item>
                        <el-button class="next-button" size="small" round type="success"
                                   @click="pageUserForm()">ادامه <i
                                class="el-icon-arrow-left el-icon-right"></i></el-button>
                    </el-form-item>
                </el-form>

                <el-form v-show="addUserForm.page == 2" label-position="top" size="small">
                    <el-form-item :class="{'input-empty': errors.has('brand') }" label="نام برند :"
                                  v-if="addUserForm.data.type == 'brand'">
                        <el-select name="brand"
                                   :default-first-option="true"
                                   data-vv-as="برند"
                                   v-validate="'required'"
                                   v-model="addUserForm.data.brand"
                                   filterable
                                   placeholder="برند"
                                   no-match-text="برند را انتخاب کنید"
                                   no-data-text="برندی وجود ندارد">
                            <el-option
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    :label="brand.name"
                                    :value="brand.id">
                            </el-option>
                        </el-select>
                        <span v-show="errors.has('brand')">{{ errors.first('brand') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('province') }" label="استان :">
                        <el-select name="province" :default-first-option="true" data-vv-as="استان"
                                   v-validate="'required'" @change="getCities()"
                                   v-model="addUserForm.data.province"
                                   filterable
                                   placeholder="استان"
                                   no-match-text="استان را انتخاب کنید"
                                   no-data-text="استانی وجود ندارد">
                            <el-option
                                    v-for="province in provinces"
                                    :key="province.id"
                                    :label="province.name"
                                    :value="province.id">
                            </el-option>
                        </el-select>
                        <span v-show="errors.has('province')">{{ errors.first('province') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('city') }" label="شهر :">
                        <el-select name="city" data-vv-as="شهر" v-validate="'required'" @change="getCities()"
                                   v-model="addUserForm.data.city"
                                   filterable
                                   placeholder="شهر"
                                   no-match-text="شهر را انتخاب کنید"
                                   no-data-text="شهری وجود ندارد">
                            <el-option
                                    v-for="city in cities"
                                    :key="city.id"
                                    :label="city.name"
                                    :value="city.id">
                            </el-option>
                        </el-select>
                        <span v-show="errors.has('city')">{{ errors.first('city') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('local') }" label="منطقه :">
                        <el-input name="local" data-vv-as="منطقه" v-validate="'min:5'"
                                  v-model="addUserForm.data.local"></el-input>
                        <span v-show="errors.has('local')">{{ errors.first('local') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('address') }" label="آدرس :">
                        <el-input name="address" data-vv-as="آدرس" v-validate="'required|min:5'"
                                  v-model="addUserForm.data.address"></el-input>
                        <span v-show="errors.has('address')">{{ errors.first('address') }}</span>
                    </el-form-item>
                    <br>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveUser()" size="small"
                                       round type="success">{{addUserForm.operation == 'add'?'ثبت نام':'بروزرسانی'}} <i
                                    class="el-icon-arrow-left el-icon-right"></i></el-button>
                            <el-button size="small" round type="success" @click="addUserForm.page=1"><i
                                    class="el-icon-arrow-right el-icon-left"></i> بازگشت
                            </el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                users: [],
                loading: false,
                addUserForm: {
                    page: 1,
                    mode: '',
                    operation: 'add',
                    visible: false,
                    title: '',
                    loading: false,
                    data: {
                        id: '',
                        name: '',
                        type: 'brand',
                        brand: '',
                        email: '',
                        phone: '',
                        mobile: '',
                        password: '',
                        province: '',
                        city: '',
                        local: '',
                        address: '',
                    }
                },
                provinces: [],
                filters: {
                    role: '',
                    name: '',
                    page: 1,
                },
                paginateUser: [],
                brands: [],
                cities: [],
            }
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            getFilterUser(){

                var vm = this;
                var fi = vm.filters;
                vm.loading = true
                axios.get(`/v1/users/filter/name=${fi.name}/page=${fi.page}/role=${fi.role}`).then(function (response) {
                    vm.paginateUser = response.data
                    vm.users = response.data.data
                    vm.loading = false;
                })
            },
            addUser(){
                this.getbrands();
                this.getProvinces();
                this.addUserForm.data = {
                    id: '',
                    name: '',
                    type: 'brand',
                    brand: '',
                    email: '',
                    phone: '',
                    mobile: '',
                    password: '',
                    province: '',
                    city: '',
                    local: '',
                    address: '',
                }
                this.addUserForm.title = 'افزودن کاربر';
                this.addUserForm.operation = 'add';
                this.addUserForm.visible = true;

            },
            getbrands(){
                var vm = this;
                axios.get('/v1/brands/get-brands').then(function (response) {
                    vm.brands = response.data;
                });
            },
            pageUserForm(){
                var vm = this;
                var form = vm.addUserForm.data;
                this.$validator.validateAll({
                    name: form.name,
                    type: form.type,
                    email: form.email,
                    phone: form.phone,
                    mobile: form.mobile,
                    password: form.password,
                }).then((result) => {
                    if (result) {
                        vm.addUserForm.page = 2;
                    }
                });
            },
            getUsers(){
                var vm = this;
                vm.loading = true;
                axios.get('/v1/users/get-users').then(function (response) {
                    vm.users = response.data;
                    vm.loading = false;
                })
            },
            editUser(user){
                this.getProvinces();
                this.getbrands();
                var form = this.addUserForm.data;
                form.id = user.id;
                form.name = user.name;
                form.type = user.type;
                form.brand = user.brand_id;
                form.email = user.email;
                form.phone = user.phone;
                form.mobile = user.mobile;
                form.province = user.address.province;
                form.city = user.address.city;
                form.local = user.address.region;
                form.address = user.address.address;
                this.addUserForm.title = 'ویرایش کاربر';
                this.addUserForm.operation = 'edit';
                this.addUserForm.visible = true;

            },
            saveUser(){
                var vm = this;
                var form = vm.addUserForm.data;
                var form2 = {
                    province: form.province,
                    city: form.city,
                    local: form.local,
                    address: form.address,
                }
                if (form.type == 'brand') {
                    form2 = {
                        brand: form.brand,
                        province: form.province,
                        city: form.city,
                        local: form.local,
                        address: form.address
                    }
                }

                if (vm.addUserForm.operation == 'add') {

                    this.$validator.validateAll(form2).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/users/store', vm.addUserForm.data).then(function (response) {
                                if (response.status == 200) {
                                    vm.users.push(response.data);
                                    vm.$notify({
                                        title: 'ایجاد کاربر',
                                        message: 'ایجاد کاربر با موفقیت انجام شد.',
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
                if (vm.addUserForm.operation == 'edit') {
                    this.$validator.validateAll(form2).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/users/update', vm.addUserForm.data).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: ' بروزرسانی',
                                        message: 'بروزرسانی اطلاعات کاربر با موفقیت انجام شد.',
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
            getProvinces(){
                var vm = this;
                axios.get('/v1/users/get-provinces').then(function (response) {
                    vm.provinces = response.data;
                })
            },
            getCities(){
                var vm = this;
                axios.get(`/v1/users/get-cities/${vm.addUserForm.data.province}`).then(function (response) {
                    vm.cities = response.data;
                })
            },
            changePage(page){
                this.filters.page = page;
                this.getFilterUser();
            },
            deleteUser(user, index){
                var vm = this;
                this.$confirm('از حذف کاربر اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    type: 'warning'
                }).then(() => {
                    axios.post('/v1/users/delete', {id: user.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.users.splice(index, 1);
                            vm.$notify({
                                title: 'حذف کاربر',
                                message: 'کاربر با موفقیت حذف شد.',
                                type: 'success',
                            });
                        }
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'حذف لغو شد.'
                    });

                });
            }

        },
        mounted(){
            if (!this.auth) {
                this.$router.go(-1);
            }
            if (this.checkRole('admin') == -1) {
                this.$router.go(-1);
            }
            this.getFilterUser();
        }
    }
</script>

<style>
    .form-buttons {
        display: flex;
        justify-content: space-between;
        flex-direction: row-reverse;
    }

    .next-button {
        float: left;
    }

    label {
        margin-bottom: -15px;
    }

    .el-dialog__body {
        padding: 15px 20px;
    }

    .table-head {
        display: flex;
        flex-direction: row-reverse;
    }
</style>