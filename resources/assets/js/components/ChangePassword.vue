<template>
    <div>
        <el-card class="box-card">
            <h4 class="heading-order-part">تغییر رمز عبور</h4>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">

                    <br>
                    <el-form label-position="top" size="small">
                        <el-form-item :class="{'input-empty': errors.has('old') }" label="رمز عبور جاری :">
                            <el-input
                                    placeholder="رمز عبور جاری"
                                    suffix-icon="fa fa-lock"
                                    v-model="addUserForm.data.old"
                                    v-validate="'required|min:6'"
                                    name="old"
                                    type="password"
                                    data-vv-as="رمز عبور جاری">
                            </el-input>
                            <span v-show="errors.has('old')">{{ errors.first('old') }}</span>
                        </el-form-item>

                        <el-form-item :class="{'input-empty': errors.has('new') }" label="رمز عبور جدید :">
                            <el-input
                                    placeholder="رمز عبور جدید"
                                    suffix-icon="fa fa-lock"
                                    v-model="addUserForm.data.new"
                                    v-validate="'required|min:5'"
                                    name="new"
                                    type="password"
                                    data-vv-as="رمز عبور جدید">
                            </el-input>
                            <span v-show="errors.has('new')">{{ errors.first('new') }}</span>
                        </el-form-item>

                        <el-form-item :class="{'input-empty': errors.has('confirm') }" label="تکرار رمز عبور جدید :">
                            <el-input
                                    placeholder="تکرار رمز عبور جدید"
                                    suffix-icon="fa fa-lock"
                                    v-model="addUserForm.data.confirm"
                                    v-validate="'confirmed:new'"
                                    name="confirm"
                                    type="password"
                                    data-vv-as="تکرار رمز عبور جدید">
                            </el-input>
                            <span v-show="errors.has('confirm')">{{ errors.first('confirm') }}</span>
                        </el-form-item>

                        <br>
                        <el-form-item>
                            <div class="form-buttons">
                                <el-button  v-loading.fullscreen.lock="fullscreenLoading" @click="changePass()" size="small" round type="success">بروزرسانی </el-button>
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
                <div class="col-md-4"></div>
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
                    data: {
                        old: '',
                        new: '',
                        confirm: '',
                    }
                },
                provinces: [],
                cities: [],
                user: []
            }
        },
        mounted(){

        },
        methods: {
            changePass(){
                var vm = this;
                var form = vm.addUserForm.data;
                this.$validator.validateAll(form).then((result) => {
                    if (result) {
                        vm.fullscreenLoading =true;
                        axios.post('/v1/users/password/update', vm.addUserForm.data).then(function (response) {
                            if (response.data == 'ok') {
                                vm.$notify({
                                    title: ' بروزرسانی',
                                    message: 'بروزرسانی رمز عبور با موفقیت انجام شد.',
                                    type: 'success',
                                });
                                vm.fullscreenLoading =false;

                            }else {
                                vm.$notify({
                                    title: 'خطا',
                                    message: 'رمز عبور جاری درست نمی باشد.',
                                    type: 'error',
                                });
                                vm.fullscreenLoading =false;

                            }
                        })
                    }
                });
            },

        }
    }
</script>

<style scoped>

    .form-buttons {
        display: flex;
        justify-content: space-between;
        flex-direction: row-reverse;
    }
    .box-card{
        margin-bottom: 15px;
    }
</style>