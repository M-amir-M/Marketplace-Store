<template>
    <div id="pages">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">صفحه ها
                        <div class="pull-left table-head">
                            <el-button size="mini" body="primary" @click="addPage()"><span
                                    class="glyphicon glyphicon-plus"></span> افزودن صفحه
                            </el-button>
                            &nbsp;
                            <el-input
                                    size="small"
                                    placeholder="جستجوی صفحه"
                                    suffix-icon="el-icon-search"
                                    v-model="filters.term"
                            >
                            </el-input>
                        </div>
                    </div>
                    <div class="panel-body">
                        <el-table
                                v-loading="loading"
                                empty-text="هیچ صفحه ای وجود ندارد"
                                :data="pages"
                                border
                                style="width: 100%">
                            <el-table-column
                                    label="لینک صفحه"
                            >
                                <template slot-scope="scope">
                                    <span class="page-link" style="direction: ltr;" v-text="siteUrl+'/page/'+scope.row.id"></span>
                                    <el-button size="mini" @click="copyToClipboard(siteUrl+'/page/'+scope.row.id)">کپی</el-button>
                                </template>
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
                                    <el-tooltip class="item" effect="dark" content="ویرایش صفحه" placement="top-start">
                                        <el-button @click="editPage(scope.row)" body="primary" size="mini" plain
                                                   round icon="fa fa-pencil"></el-button>
                                    </el-tooltip>
                                    <el-tooltip class="item" effect="dark" content="حذف صفحه" placement="top-start">
                                        <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="deletePage(scope.row,scope.$index)" body="danger" plain
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
                        <!--:total="paginatePage.total"-->
                        <!--:page-size="paginatePage.per_page"-->
                        <!--:current-page="filters.page"-->
                        <!--@current-change="changePage"-->
                        <!--&gt;-->
                        <!--</el-pagination>-->


                    </div>
                </div>
            </div>
        </div>

        <el-dialog :fullscreen="true" title="افزودن صفحه" :visible.sync="addPageForm.visible">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <el-form label-position="top" size="small"
                >
                    <el-form-item :class="{'input-empty': errors.has('title') }" label="عنوان صفحه :">
                        <el-input
                                v-model="addPageForm.data.title"
                                name="title"
                                data-vv-as="عنوان صفحه"
                                v-validate="'required|min:5'"
                        ></el-input>
                        <span v-show="errors.has('title')">{{ errors.first('title') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('body') }" label="متن :">
                        <ckeditor
                                name="body"
                                data-vv-as="متن صفحه"
                                v-validate.initial="'required|min:5'"
                                v-model="addPageForm.data.body"
                                :config="config">
                        </ckeditor>
                        <span v-show="errors.has('body')">{{ errors.first('body') }}</span>
                    </el-form-item>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-loading.fullscreen.lock="fullscreenLoading" @click="savePage()" size="small" round body="success">ثبت صفحه<i
                                    class="el-icon-arrow-left el-icon-right"></i></el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    import Ckeditor from 'vue-ckeditor2';
    export default {
        components: { Ckeditor },
        data(){
            return {
                auth: window.Laravel.auth,
                config: {
                    toolbar: [
                        {
                            name: 'document',
                            items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates']
                        },
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                        },
                        {name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']},
                        {
                            name: 'forms',
                            items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField']
                        },
                        '/',
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']
                        },
                        {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                        {
                            name: 'insert',
                            items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']
                        },
                        '/',
                        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
                        {name: 'colors', items: ['TextColor', 'BGColor']},
                        {name: 'tools', items: ['Maximize', 'ShowBlocks']},
                        {name: 'about', items: ['About']}
                    ],
                    uiColor: '#9AB8F3',
                    language: 'fa'
                },
                addPageForm: {
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
                siteUrl:window.location.host,
                fullscreenLoading: false,
                loading: false,
                pages: [],
                filters: {
                    term: '',
                    page: 1,
                    category_ids: '',
                    page_ids: [],
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
            this.getPages();
        },
        methods: {
            checkRole(role){
                return window.Laravel.user.role_user.indexOf(role);
            },
            copyToClipboard(text) {
//                e.target.parentNode.querySelector(".page-link").innerHTML.select();
                var element = document.createElement('textarea');
                element.value = text;
                document.body.appendChild(element);
                element.select();
                document.execCommand('copy');
                element.remove();
                this.$message({
                    title: 'کپی',
                    message: 'لینک کپی شد.',
                    body: 'success',
                });
            },
            addPage(){
                this.addPageForm.operation = 'add';
                this.addPageForm.data = {
                    id: '',
                    title: '',
                    body: '',
                }
                this.addPageForm.visible = true;
                this.errors.clear();
            },
            editPage(page){
                this.addPageForm.operation = 'edit'
                var info = this.addPageForm.data;
                info.id = page.id;
                info.title = page.title;
                info.body = page.body;

                this.addPageForm.visible = true;
            },
            getPages(){
                var vm = this;
                vm.loading = true;
                axios.get(`/v1/pages/get-pages`).then(function (response) {
                    vm.pages = response.data;
                    vm.loading = false;
                })
            },
            savePage(){
                var vm = this;
                var datas = new FormData();
                var info = vm.addPageForm.data;
                datas.append('title', info.title);
                datas.append('body', info.body);
                var form = {
                    name: info.name,
                    body: info.body,
                }
                if (vm.addPageForm.operation == 'add') {
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true
                            axios.post('/v1/pages/store', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.pages.push(response.data);
                                    vm.$notify({
                                        title: 'ثبت صفحه',
                                        message: 'صفحه با موفقیت ثبت شد.',
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
                if (vm.addPageForm.operation == 'edit') {
                    datas.append('id', info.id);
                    this.$validator.validateAll(form).then((result) => {
                        if (result) {
                            vm.fullscreenLoading = true;
                            axios.post('/v1/pages/update', datas).then(function (response) {
                                if (response.status == 200) {
                                    vm.$notify({
                                        title: 'بروزرسانی صفحه',
                                        message: 'صفحه با موفقیت بروزرسانی شد.',
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
            changePage(page){
                this.filters.page = page;
                this.getPages();
            },
            deletePage(page, index){
                var vm = this;
                this.$confirm('از حذف صفحه اطمینان دارید؟', 'اخطار', {
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                    body: 'warning'
                }).then(() => {
                    vm.fullscreenLoading = true;
                    axios.post('/v1/pages/delete', {id: page.id}).then(function (response) {
                        if (response.status == 200) {
                            vm.pages.splice(index, 1);
                            vm.$notify({
                                title: 'حذف صفحه',
                                message: 'صفحه با موفقیت حذف شد.',
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