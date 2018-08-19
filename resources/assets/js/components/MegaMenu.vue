<template>
    <div>
        <nav class="navbar  navbar-default" role="navigation">
            <div class="navbar-header hidden-md hidden-sm hidden-lg ">
                <div class="navbar-header-right">
                    <a href="/">
                        <img class="img-responsive" src="https://www.easystore.co/favicon.png" alt="" width="30"
                             height="30">
                    </a>
                    <a href="#">
                        <el-button size="mini"
                                   round
                                   @click.prevent="handleCollapse('#bs-megadropdown-tabs')"
                                   type="primary"
                                   class="navbar-toggle collapsed"
                                   data-toggle="collapse"
                                   data-target="#bs-megadropdown-tabs">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="fa fa-bars"></span>
                        </el-button>

                    </a>

                </div>
                <div class="navbar-header-left">
                    <router-link v-if="!auth" :to="{name:'pages.login'}">
                        <span class="fa fa-sign-in"></span>
                    </router-link>
                    <router-link v-if="!auth" :to="{name:'pages.register'}">
                        <span class="fa fa-user"></span>
                    </router-link>
                    <router-link v-if="auth" :to="{name:'dashboard.orders'}">
                        <span class="fa fa-dashboard"></span>
                    </router-link>
                    <router-link :to="auth ? {name : 'checkout'}:{name:'pages.login'}">
                        <el-badge :value="$parent.cartNumber" class="item">
                            <span class="fa fa-shopping-cart"></span>
                        </el-badge>
                    </router-link>
                    <a href="#"
                       @click.prevent="handleCollapse('#search-collapse-input')"
                       data-toggle="collapse"
                       data-target="#search-collapse-input"
                    ><span class="fa fa-search"></span></a>
                </div>
            </div>
            <div class="hidden-md hidden-sm hidden-lg ">
                <el-menu
                        id="bs-megadropdown-tabs"
                        default-active="2"
                        class="collapse navbar-collapse"
                        background-color="#fff"
                        text-color="#999"
                        active-text-color="#ffd04b">

                    <el-submenu index="1">
                        <template slot="title">
                            <span>تولید کنندگان</span>
                        </template>
                        <el-menu-item class="brand-item" v-for="(brand,index) in brands" :key="index" :index="'1-'+index">
                            <a @click.prevent="brandProduct(brand)" href="#" v-text="brand.name"></a>
                        </el-menu-item>
                    </el-submenu>
                    <el-submenu index="2">
                        <template slot="title">
                            <span>پکیج ها</span>
                        </template>

                        <el-menu-item class="brand-item" v-for="(pack,index) in packs" :key="index" :index="'1-'+index">
                            <router-link
                                    v-text="pack.name"
                                    :to="{name: 'product.detail',params:{title:replaceAll(pack.name,' ', '-'),id:pack.id,type:'package'}}">

                            </router-link>
                        </el-menu-item>
                    </el-submenu>
                    <div v-for="category in categories">
                        <el-submenu v-if="category.child.length > 0" class="cat-level1" :key="category.id"
                                    :index="'l1'+category.id">
                            <template slot="title">
                            <span>
                                <a @click.prevent="categoryProduct(category)"
                                   data-toggle="collapse"
                                   data-target="#bs-megadropdown-tabs"
                                   class="cat-link"
                                   href="#"
                                   v-text="category.name"></a>
                            </span>
                            </template>
                            <div v-for="child in category.child">
                                <el-submenu v-if="child.child.length > 0"
                                            class="cat-level2"
                                            :key="child.id" :index="'l2'+child.id">
                                    <template slot="title">
                            <span>
                                <a @click.prevent="categoryProduct(child)"
                                   data-toggle="collapse"
                                   data-target="#bs-megadropdown-tabs"
                                   class="cat-link"
                                   href="#"
                                   v-text="child.name"
                                ></a>
                            </span>
                                    </template>
                                    <div v-for="child2 in child.child">
                                        <el-menu-item class="cat-level3" :index="'1-'+child2.id"
                                                      :key="'1-'+child2.id">
                            <span>
                                <a @click.prevent="categoryProduct(child2)"
                                   data-toggle="collapse"
                                   data-target="#bs-megadropdown-tabs"
                                   class="cat-link"
                                   href="#"
                                   v-text="child2.name"></a>
                            </span>
                                        </el-menu-item>
                                    </div>
                                </el-submenu>
                                <el-menu-item v-else class="cat-level3" :index="'1-'+child.id"
                                              :key="'1-'+child.id">
                            <span>
                                <a @click.prevent="categoryProduct(child)"
                                   data-toggle="collapse"
                                   data-target="#bs-megadropdown-tabs"
                                   class="cat-link"
                                   href="#"
                                   v-text="child.name"></a>
                            </span>
                                </el-menu-item>
                            </div>
                        </el-submenu>
                        <el-menu-item class="cat-level2"
                                      v-else
                                      :key="'1-'+category.id"
                                      :index="'1-'+category.id"
                        >
                        <span>
                            <a @click.prevent="categoryProduct(category)"
                               class="cat-link"
                               data-toggle="collapse"
                               data-target="#bs-megadropdown-tabs"
                               href="#"
                               v-text="category.name"></a>
                        </span>
                        </el-menu-item>
                    </div>
                </el-menu>
            </div>

            <div class=" hidden-md hidden-sm hidden-lg">
                <div  id="search-collapse-input"
                      default-active="2"
                      class="search-bar collapse navbar-collapse">
                    <form @submit.prevent="searchProduct()">
                        <el-input size="mini" placeholder="جستجوی محصول..." name="search" v-model="filters.term">
                            <el-button type="info" native-type="submit" slot="append"
                                       icon="el-icon-search"></el-button>
                        </el-input>
                    </form>
                </div>
            </div>

            <div class="collapse navbar-collapse">
                <ul>
                    <li><a href="/"><span class="fa fa-home" style="font-size: 20px;"></span></a></li>
                    <li class="submenu"><a href="#" title="Unser Produktsortiment">تولید کنندگان</a>
                        <ul class="megamenu brands">
                            <li class="brand-item" v-for="brand in brands">
                                <a @click.prevent="brandProduct(brand)" href="#" v-text="brand.name"></a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu"><a href="#" title="Unser Produktsortiment">پکیج ها</a>
                        <ul class="megamenu brands">
                            <li class="brand-item" v-for="(pack,index) in packs" :key="index" :index="'1-'+index">
                                <router-link
                                        v-text="pack.name"
                                        :to="{name: 'product.detail',params:{title:replaceAll(pack.name,' ', '-'),id:pack.id,type:'package'}}">

                                </router-link>
                            </li>
                        </ul>
                    </li>
                    <li v-for="category in categories" :class="category.child.length > 0 ? 'submenu':'' ">
                        <a href="#" @click.prevent="categoryProduct(category)" v-text="category.name"
                           class="category-parent"></a>
                        <ul v-if="category.child.length > 0" class="megamenu">
                            <div v-for="child in category.child">
                                <a href="#" @click.prevent="categoryProduct(child)" v-text="child.name"
                                   class="first-category-child"></a>
                                <hr>
                                <div v-if="child.child.length > 0">
                                    <div v-for="child2 in child.child"><a @click.prevent="categoryProduct(child2)"
                                                                          class="second-category-child" href="#"
                                                                          v-text="child2.name"></a></div>
                                </div>
                            </div>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </div>
</template>


<script>
    export default{
        data(){
            return {
                auth: window.Laravel.auth,
                categories: [],
                brands: [],
                filters: {
                    term: '',
                    page: 1,
                    category_ids: '',
                    brand_ids: [],
                },
                packs:[]
            }
        },
        mounted(){
            this.getCategories();
            this.getBrands();
            this.getPackages();
        },
        methods: {
            handleOpen(key, keyPath) {
                console.log(key, keyPath);
            },
            handleClose(key, keyPath) {
                console.log(key, keyPath);
            },
            handleCollapse(el){
                if(el == '#search-collapse-input'){
                    $('#bs-megadropdown-tabs').collapse('hide');
                    $(el).collapse('toggle');
                }
                if(el == '#bs-megadropdown-tabs'){
                    $('#search-collapse-input').collapse('hide');
                    $(el).collapse('toggle');
                }
            },
            searchProduct(){
                $('#search-collapse-input').collapse('hide');
                this.$router.push({path: `/search/${this.filters.term}/page/1`, params: {term: this.filters.term}});
            },
            categoryProduct(category){
                this.$router.push({
                    path: `/category/${this.replaceAll(category.name, " ", '-')}/${category.id}/page/1`,
                    params: {category_id: category.id, category_name: category.name}
                });
            },
            brandProduct(brand){
                this.$router.push({
                    path: `/brand/${this.replaceAll(brand.name, " ", '-')}/${brand.id}/page/1`,
                    params: {brand_id: brand.id, brand_name: brand.name}
                });
            },
            getPackages(){
                var vm = this;
                axios.get(`/v1/packages/get-packages`).then(function (response) {
                    vm.packs = response.data
                });
            },
            replaceAll(str, find, replace) {
                return str.replace(new RegExp(find, 'g'), replace);
            },
            getCategories(){
                var vm = this;

                axios.get('/v1/categories/get-categories').then(function (response) {
                    vm.categories = response.data;
                }).catch(function (error) {

                });
            },
            getBrands(){
                var vm = this;

                axios.get('/v1/brands/get-brands').then(function (response) {
                    vm.brands = response.data;
                }).catch(function (error) {

                });
            },
        }
    }
</script>

<style>
    /* first stage */

    nav > .collapse > ul {
        position: relative;
        display: flex;
        justify-content: flex-start;
        width: auto;
        margin: 0 auto;
        padding: 0;
    }

    nav a {
        display: block;
        font-size: 14px;
        text-decoration: none !important;
    }

    nav ul li {
        color: #fff;
        list-style: none;
        transition: 0.5s;
    }

    nav > .collapse > ul > li > a {
        padding: 5px 15px;
        text-decoration: none;
        color: #fff;
    }

    nav ul > li.submenu > a:after {
        position: relative;
        float: right;
        content: '';
        margin-left: 10px;
        margin-top: 8px;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid #fff;
        border-bottom: 5px solid transparent;
    }

    nav ul > li.submenu:hover > a:after {
        margin-top: 2px;
        border-top: 5px solid transparent;
        border-bottom: 5px solid #fff;
    }

    nav > .collapse > ul > li:hover {
        background: #4096ee;
    }

    /* second stage (the mega-menu) */

    nav ul.megamenu {
        position: absolute;
        display: flex;
        flex-wrap: wrap;
        top: -9999px;
        left: 0;
        padding: 10px 40px 15px 40px;
        background: #fff;
        color: #000;
        text-align: right;
        border-top: 5px solid #4096ee;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
        z-index: 10000;
        width: 100%
    }

    nav ul li:hover ul.megamenu {
        top: 100%;
    }

    /* third stage (child-menus in the mega-menu) */

    nav ul.megamenu ul {
        width: 25%;
        margin-bottom: 40px;
        color: #000;
        box-shadow: none;
    }

    nav ul.megamenu h4 {
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    nav ul.megamenu ul li {
        display: block;
    }

    nav ul.megamenu ul li a {
        margin-top: 10px;
        transition: 0.5s;
        text-align:right;
    }

    nav ul.megamenu ul li a:hover {
        color: #4096ee;
    }

    nav ul.megamenu > div {
        margin-left: 25px;
        min-width: 100px;
    }

    .second-category-child a {
        color: #ccc !important;
    }

    ul.brands {
        display: flex;
        flex-direction: column;
    }

    li.brand-item {
        color: #000;
        text-align: right !important;
    }

    li.brand-item a {
        text-align: right !important;
    }

    .navbar-header {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
    }

    .navbar-header-left {
        display: flex;
        justify-content: flex-start;
        flex-direction: row-reverse;
        align-items: center;
        width: 100%;
    }

    .navbar-header-right {
        display: flex;
        justify-content: flex-end;
        flex-direction: row-reverse;
        align-items: center;
        width: 100%;
    }

    .navbar-header-left a {
        color: white;
        font-size: 20px;
        margin-right: 10px;
    }

    .navbar-header-right a {
        color: white;
        font-size: 20px;
        margin-left: 10px;
    }

    .el-submenu__icon-arrow {
        left: 20px;
        right: auto;
    }

    .cat-level2, .cat-level3 {
        padding-right: 20px;
    }
    .cat-link {
        display: inherit;
    }

    .search-bar {
        width: 100%;
    }

    .search-bar .el-input-group__append {
        border-radius: 5px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border: 1px solid #eee;
        border-left: 0;

    }
    .search-bar .el-input-group--append .el-input__inner {
        border-radius: 5px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border: 1px solid #eee;
        border-right:0;
    }

</style>