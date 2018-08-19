<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <el-tabs type="border-card">
                    <el-tab-pane>
                        <span slot="label"><i class="fa fa-gear"></i> عمومی</span>
                        <div class="setting">{{settings['site-name'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="settings['site-name'].value"></i>
                            <el-button size="mini" @click="editSetting('site-name','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['site-logo'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="settings['site-logo'].value"></i>
                            <el-button size="mini" @click="editSetting('site-logo','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['support-phone'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['support-phone'].value"></i>
                            <el-button size="mini" @click="editSetting('support-phone','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['health-page-link'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['health-page-link'].value"></i>
                            <el-button size="mini" @click="editSetting('health-page-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['health-dialog-status'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['health-dialog-status'].value == 0 ? 'غیرفعال':'فعال'"></i>
                            <el-button size="mini" @click="editSetting('health-dialog-status','boolean')">تغییر وضعیت</el-button>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane>
                        <span slot="label"><i class="fa fa-instagram"></i> شبکه های اجتماعی</span>
                        <div class="setting">{{settings['telegram-link'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['telegram-link'].value"></i>
                            <el-button size="mini" @click="editSetting('telegram-phone','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['instagram-link'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['instagram-link'].value"></i>
                            <el-button size="mini" @click="editSetting('instagram-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['twitter-link'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['twitter-link'].value"></i>
                            <el-button size="mini" @click="editSetting('twitter-link','string')">ویرایش</el-button>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane>
                        <span slot="label"><i class="fa fa-file "></i> فوتر سایت </span>
                        <div class="setting">{{settings['contact-us-link'].label}} : <i class="title"></i><i
                                class="value" v-text="settings['contact-us-link'].value"></i>
                            <el-button size="mini" @click="editSetting('contact-us-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['about-us-link'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['about-us-link'].value"></i>
                            <el-button size="mini" @click="editSetting('about-us-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['law-link'].label}} : <i class="title"></i><i class="value"
                                                                                                      v-text="settings['law-link'].value"></i>
                            <el-button size="mini" @click="editSetting('law-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['questions-link'].label}} : <i class="title"></i><i
                                class="value" v-text="settings['questions-link'].value"></i>
                            <el-button size="mini" @click="editSetting('questions-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['enamad-link'].label}} : <i class="title"></i><i class="value"
                                                                                                         v-text="settings['enamad-link'].value"></i>
                            <el-button size="mini" @click="editSetting('enamad-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['senfi-link'].label}} : <i class="title"></i><i class="value"
                                                                                                        v-text="settings['senfi-link'].value"></i>
                            <el-button size="mini" @click="editSetting('senfi-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['samandehi-link'].label}} : <i class="title"></i><i
                                class="value" v-text="settings['samandehi-link'].value"></i>
                            <el-button size="mini" @click="editSetting('samandehi-link','string')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['shaparak-link'].label}} : <i class="title"></i><i class="value"
                                                                                                           v-text="settings['shaparak-link'].value"></i>
                            <el-button size="mini" @click="editSetting('shaparak-link','string')">ویرایش</el-button>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane>
                        <span slot="label"><i class="fa fa-star"></i> ستاره مشتریان</span>
                        <p>حداقل مبلغ برای تعداد ستاره را مشخص کنید.</p>
                        <br>
                        <div class="setting">{{settings['1-star'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="formatPrice(settings['1-star'].value)"></i>
                            <el-button size="mini" @click="editSetting('1-star','integer')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['2-star'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="formatPrice(settings['2-star'].value)"></i>
                            <el-button size="mini" @click="editSetting('2-star','integer')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['3-star'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="formatPrice(settings['3-star'].value)"></i>
                            <el-button size="mini" @click="editSetting('3-star','integer')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['4-star'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="formatPrice(settings['4-star'].value)"></i>
                            <el-button size="mini" @click="editSetting('4-star','integer')">ویرایش</el-button>
                        </div>
                        <div class="setting">{{settings['5-star'].label}} : <i class="title"></i><i class="value"
                                                                                                       v-text="formatPrice(settings['5-star'].value)"></i>
                            <el-button size="mini" @click="editSetting('5-star','integer')">ویرایش</el-button>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>

        <el-dialog title="ویرایش تنظیمات" :visible.sync="settingForm.visible">
            <el-form v-if="settingForm.data.key != ''" label-position="top" size="small"
            >
                <el-form-item v-if="settingForm.data.type == 'boolean'" :label="settings[settingForm.data.key].label +':'">
                    <el-switch
                            style="display: block"
                            v-model="settingForm.data.value"
                            active-color="#13ce66"
                            inactive-color="#ff4949"
                            active-value="1"
                            inactive-value="0"
                            active-text="فعال"
                            inactive-text="غیر فعال">
                    </el-switch>

                </el-form-item>
                <el-form-item v-if="settingForm.data.type == 'string'" :label="settings[settingForm.data.key].label +':'">
                    <el-input
                            v-model="settingForm.data.value"
                    ></el-input>
                </el-form-item>
                <el-form-item v-if="settingForm.data.type == 'integer'" :class="{'input-empty': errors.has('value')}" :label="settings[settingForm.data.key].label +':'">
                    <el-input
                            v-model="settingForm.data.value"
                            name="value"
                            :data-vv-as="settings[settingForm.data.key].label"
                            v-validate="'numeric'"
                    ></el-input>
                    <span v-show="errors.has('value')">{{ errors.first('value') }}</span>
                </el-form-item>

                <el-form-item>
                    <div class="form-buttons">
                        <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="saveSetting()" size="small"
                                   round type="success">ثبت تنظیمات<i
                                class="el-icon-arrow-left el-icon-right"></i></el-button>
                    </div>

                </el-form-item>
            </el-form>
        </el-dialog>

    </div>
</template>

<script>
    export default {
        data(){
            return {
                auth: window.Laravel.auth,
                settingForm: {
                    visible: false,
                    title: '',
                    loading: false,
                    data: {
                        type:'string',
                        key: '',
                        value: '',
                    }
                },
                fullscreenLoading: false,
                loading: false,
                activeTab: 'general',
                settings: [],
            }

        },
        created(){
            this.getSettings();
        },
        mounted(){
            if (!this.auth) {
                this.$router.go(-1);
            }
            if (this.checkRole('admin') == -1) {
                this.$router.go(-1);
            }
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            formatPrice(price){
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+" تومان ";
            },
            editSetting(key, type){
                var info = this.settingForm.data;
                info.key = key;
                info.type = type;
                info.value = this.settings[key].value;
                this.settingForm.visible = true;
            },
            getSettings(){
                var vm = this;
                vm.loading = true;
                axios.get(`/v1/settings/get-settings`).then(function (response) {
                    vm.settings = response.data;
                    vm.loading = false;
                })
            },
            saveSetting(){
                var vm = this;
                var info = this.settingForm.data;
                var form = {
                    key: info.key,
                    value: info.value
                }
                this.$validator.validateAll(form).then((result) => {
                    if (result) {
                        vm.fullscreenLoading = true;
                        axios.post('/v1/settings/update', form).then(function (response) {
                            if (response.status == 200) {
                                vm.$notify({
                                    title: 'بروزرسانی تنظیمات',
                                    message: 'تنظیمات با موفقیت بروزرسانی شد.',
                                    type: 'success',
                                });
                                vm.settings[info.key].value = info.value;
                                vm.settingForm.visible = false;
                                vm.fullscreenLoading = false;
                            }
                        }).catch(function (errors) {
                            vm.settingForm.visible = false;
                            vm.fullscreenLoading = false;
                        });
                    }
                });

            },
            changePage(page){
                this.filters.page = page;
                this.getSettings();
            },
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

    .setting {
        padding: 15px 0;
        border-bottom: 1px dashed #ccc;
    }

    .el-tabs__nav {
        float: right !important;
    }

    .setting .value {

    }
</style>