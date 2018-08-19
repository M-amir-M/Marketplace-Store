<template>
    <div>
        <div class="row">
            <el-card class="box-card">
                <div slot="header">
                    <div>
                        <span class="icon" :class="headerIcon"></span>&nbsp;&nbsp;
                        <span v-text="headerTitle"></span>
                    </div>
                </div>
                <div class="">
                    <el-carousel :height="box_height" :autoplay="false" indicator-position="none">
                        <div class=" products-box">
                            <div class="row">
                                <div v-for="(products,index) in chunkedProduct">
                                    <el-carousel-item :key="index">
                                        <div class="product-box">
                                            <div class=" col-md-3 col-sm-4 col-xs-6" v-for="product in products">
                                                <product :add-cart="addCart" :product="product"></product>
                                            </div>
                                        </div>
                                    </el-carousel-item>
                                </div>

                            </div>
                        </div>
                    </el-carousel>
                </div>
            </el-card>
        </div>
    </div>


</template>

<script>
    import Product from './Product.vue';

    export default {
        components: {
            'product': Product
        },
        props: ['products', 'headerTitle', 'headerIcon', 'addCart'],
        data(){
            return {
                box_height: '345px'
            }
        },
        mounted() {
            if (screen.width < 768 && screen.width > 0)
                this.box_height = '280px';
        },
        computed: {
            chunkedProduct() {
                var column = 4;
                if (screen.width < 768 && screen.width > 0)
                    column = 2;
                if(screen.width > 768 && screen.width < 992)
                    column = 3;
                if(screen.width > 992)
                    column = 4;
                return _.chunk(this.products, column);
            }
        }
    }
</script>

<style>
    .icon {
        font-size: 25px;
    }

    .el-card__header {
        padding: 5px 15px;
        background-color: #ccc;
        color: white;
    }

    .el-card__body {
        padding: 0;
    }

    .product-box {
        margin: 15px 0;
    }

    .col-md-3 {
        padding-right: 10px;
        padding-left: 10px;
    }

    .el-carousel__item:nth-child(2n) {
        background-color: #fff;
    }

    .el-carousel__item:nth-child(2n+1) {
        background-color: #fff;
    }

    .el-carousel__item {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    @media (max-width: 768px) {
        .products-box{
            margin-top: 15px;
        }
    }
</style>