<template>
    <div>
        <el-card class="box-card order-list">
            <div class="info-show" v-if="!addUserForm.visible" >
                <div class="row container info">
                    <h4 class="heading-order-part">اطلاعات فردی</h4>
                    <br>
                    <dl class="dl-horizontal">
                        <dt>نام :</dt>
                        <dd v-text="user.name"></dd>
                        <dt>تلفن همراه :</dt>
                        <dd v-text="user.mobile"></dd>
                        <dt>آدرس :</dt>
                        <dd>{{user.address.address}} - {{user.address.city_name}} - {{user.address.province_name}}</dd>
                    </dl>
                </div>
                <div class="row container">
                    <el-button size="small" @click="editUser()" type="success" round>ویرایش اطلاعات</el-button>
                </div>
            </div>
            <div class="info-edit" v-if="addUserForm.visible">

                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <h4 class="heading-order-part">ویرایش اطلاعات فردی</h4>

                        <el-form label-position="top" size="small">

                            <el-form-item :class="{'input-empty': errors.has('name') }" label="نام و نام خانوادگی :">
                                <el-input name="name" data-vv-as="نام و نام خانوادگی" v-validate="'required|min:5'"
                                          v-model="addUserForm.data.name"></el-input>
                                <span v-show="errors.has('name')">{{ errors.first('name') }}</span>
                            </el-form-item>
                            <el-form-item :class="{'input-empty': errors.has('email') }" label="ایمیل :">
                                <el-input name="email" data-vv-as="ایمیل" v-validate="'required|email'"
                                          v-model="addUserForm.data.email"></el-input>
                                <span v-show="errors.has('email')">{{ errors.first('email') }}</span>
                            </el-form-item>
                            <el-form-item :class="{'input-empty': errors.has('phone') }" label="تلفن :">
                                <el-input name="phone" data-vv-as="تلفن" v-validate="'required|numeric|min:5|max:15'"
                                          v-model="addUserForm.data.phone"></el-input>
                                <span v-show="errors.has('phone')">{{ errors.first('phone') }}</span>
                            </el-form-item>
                            <el-form-item :class="{'input-empty': errors.has('company') }" label="نام شرکت :"
                                          v-if="addUserForm.data.type == 'brand'">
                                <el-input name="company" data-vv-as="نام شرکت" v-validate="'required|min:5|max:25'"
                                          v-model="addUserForm.data.company"></el-input>
                                <span v-show="errors.has('company')">{{ errors.first('company') }}</span>
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
                                    <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveUser()" size="small" round type="success">بروزرسانی</el-button>
                                    <el-button size="small" round type="warning" @click="addUserForm.visible = false"> لغو
                                    </el-button>
                                </div>
                            </el-form-item>
                        </el-form>

                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </el-card>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                fullscreenLoading:false,
                addUserForm: {
                    page: 1,
                    visible: false,
                    data: {
                        id: '',
                        name: '',
                        email: '',
                        phone: '',
                        province: '1',
                        city: '',
                        local: '',
                        address: '',
                    }
                },
                provinces: [],
                cities: [],
                user: []
            }
        },
        mounted(){
            this.getUser();
        },
        methods: {
            getUser(){
                var vm = this;
                axios.get('/v1/users/get-auth').then(function (response) {
                    if (response.status == 200) {
                        vm.user = response.data;
                    }
                }).catch(function (error) {

                })
            },
            editUser(){
                var user = this.user
                this.getProvinces();
                var form = this.addUserForm.data;
                form.id = user.id;
                form.name = user.name;
                form.type = user.type;
                form.email = user.email;
                form.phone = user.phone;
                form.province = user.address.province;
                form.city = user.address.city;
                form.local = user.address.region;
                form.address = user.address.address;
                this.addUserForm.visible = true;

            },
            saveUser(){
                var vm = this;
                var form = vm.addUserForm.data;
                var form2 = {
                    id: form.id,
                    province: form.province,
                    city: form.city,
                    local: form.local,
                    address: form.address,
                    name : form.name,
                    email : form.email,
                    phone : form.phone,
                }

                this.$validator.validateAll(form2).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;

                        axios.post('/v1/users/profile/update', vm.addUserForm.data).then(function (response) {
                            if (response.status == 200) {
                                vm.$notify({
                                    title: ' بروزرسانی',
                                    message: 'بروزرسانی اطلاعات شما با موفقیت انجام شد.',
                                    type: 'success',
                                });
                                vm.fullscreenLoading = false;

                            }
                        }).catch(function (errors) {
                            vm.fullscreenLoading = false;

                        })
                    }
                });
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
            }
        }
    }
</script>

<style scoped>
    .info {
        border-bottom: 1px dashed #999;
        margin-bottom: 16px;
    }
    .form-buttons {
        display: flex;
        justify-content: space-between;
        flex-direction: row-reverse;
    }

    .box-card{
        margin-bottom: 15px;
    }
</style>