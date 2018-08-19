<template>
    <div>
        <div class="row">
            <br>
            <div class="container">
                <div class="panel panel-default">
                    <div class="panel-heading" v-text="page.title"> </div>
                    <div class="panel-body">
                        <div v-html="page.body"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Ckeditor from 'vue-ckeditor2';
    export default {
        components: { Ckeditor },
        data(){
            return {
                auth: window.Laravel.auth,
                fullscreenLoading: false,
                loading: false,
                page: [],
                page_id: '',
            }
        },
        mounted(){
            this.page_id = this.$route.params.id;
            this.getPage();
        },
        methods: {
            openFullScreen() {
                const loading = this.$loading({
                    lock: true,
//                    text: 'درحال بار گذاری...',
                    spinner: 'fa fa-spinner fa-pulse fa-3x fa-fw',
                    background: 'rgba(0, 0, 0, 0.7)'
                });
                return loading;
            },
            getPage(){
                var vm = this;
                vm.openFullScreen();
                axios.get(`/v1/pages/get-page/${vm.page_id}`).then(function (response) {
                    vm.page = response.data;
                    vm.openFullScreen().close();
                })
            },
        }
    }
</script>

<style>
</style>