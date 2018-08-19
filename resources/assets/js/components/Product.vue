<template>
    <div>
        <div class="product-card">
            <el-card :body-style="{ padding: '0px'}">
                <router-link :to="{name: 'product.detail',params:{title:replaceAll(product.name,' ', '-'),id:product.id,type:'product'}}">
                    <img :src="'/images/products/tn-'+product.image" class="image img-responsive" style="max-height: 200px; border-bottom:1px solid #8c8c8c">
                </router-link>
                <div class="card-content ">
                    <div class="product-info">
                        <p v-text="formatTitle(product.name)" class="title"></p>
                        <a href="#" class="brand" @click.prevent="brandProduct(product.brand)"><p
                                v-text="product.brand.name" class="title"></p></a>
                        <p class="price" v-text="product.amount">   </p>
                        <div class="text-center">
                            <el-rate
                                    v-model="product.rate"
                                    disabled
                                    text-color="#ff9900">
                            </el-rate>
                        </div>

                    </div>
                    <div class="add-to-cart">
                        <div @click="addCart(product,'product')" class="bottom ">
                        <span class="price">
                            <span  v-text="formatPrice(product.price)"></span>
                        </span>
                            <el-button type="text" class="button"><span class="fa fa-plus"></span></el-button>
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
    </div>


</template>

<script>
    export default {
        props: ['product', 'addCart'],
        data(){
            return {}
        },
        mounted() {
//            console.log('Component mounted.')
        },
        methods: {
            formatTitle(title){
                if (title.length > 10) {
                    return title.substring(0, 15) + '...';
                }
                return title;
            },
            formatPrice(price){
                if (price != "")
                    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + " تومان ";
                return "";
            },
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            brandProduct(brand){
                this.$router.push({
                    path: `/brand/${this.replaceAll(brand.name, " ", '-')}/${brand.id}/page/1`,
                    params: {brand_id: brand.id, brand_name: brand.name}
                });
            },
        }
    }
</script>

<style>
    .card-content .title, .card-content .bottom {
        padding: 0 10px;
        margin: 0
    }

    .title {
        font-size: 12px;
        text-align: center;
        padding-top: 0;
    }

    .bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 30px;
        background-color: #ddd;
        color: #3c557d;
        border-top: 1px solid #ccc;
        cursor: pointer;
    }

    .price {
        font-size: 12px;
        text-align: center;
    }

    a {
        font-size: 12px;
        text-align: center;
        text-decoration: none !important;

    }
</style>