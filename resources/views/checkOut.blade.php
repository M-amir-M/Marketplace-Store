@extends('layouts.main')

@section('content')
    <div class="row">
        <section id="checkout">
            <div class="container">


                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                @endif


                <el-card class="box-card">
                    <div slot="header">
                        <div>
                            <span class=" icon fa fa-shopping-cart"></span>&nbsp;&nbsp;
                            <span>اطلاعات سفارشات</span>
                        </div>
                    </div>

                    <div class="row">
                        <h4 class="heading-order-part">اطلاعات گیرنده سفارش</h4>
                        <br>
                        <dl class="dl-horizontal">
                            <dt>نام :</dt>
                            <dd v-if="user.name != null" v-text="user.name"></dd>
                            <dd v-if="user.name == null">
                                <el-form :inline="true" class="demo-form-inline" size="mini">
                                    <el-form-item :class="{'input-empty': errors.has('name') }" label="">
                                        <el-input name="name" data-vv-as="نام و نام خانوادگی"
                                                  v-validate="'required|min:5'" v-model="orderInfo.name"
                                                  placeholder="نام و نام خانوادگی"></el-input>
                                        <span v-show="errors.has('name')">@{{ errors.first('name') }}</span>
                                    </el-form-item>
                                    <el-form-item label="">
                                        <el-button v-loading.fullscreen.lock="fullscreenLoading"
                                                   v-on:click.prevent="saveName()"> ذخیره نام <span
                                                    class="fa fa-user-plus"></span>
                                        </el-button>
                                    </el-form-item>
                                </el-form>
                            </dd>
                            <dt>تلفن همراه :</dt>
                            <dd v-text="user.mobile"></dd>
                            <dt>آدرس :</dt>
                            <dd v-if="user.address != null">
                                @{{user.address.province_name}} - @{{user.address.city_name}}
                                - @{{user.address.address}}
                                <a href="#" @click.prevent="editAddress()"><span
                                            class="fa  fa-pencil-square-o "></span></a>
                            </dd>
                            <dd v-if="user.address == null" :class="{'input-empty': errors.has('address_id') }">
                                <input name="address_id"
                                       type="hidden"
                                       data-vv-as="آدرس"
                                       v-validate="'required'"
                                       v-model="orderInfo.address_id"
                                       placeholder="آدرس">
                                <span v-show="errors.has('address_id')">@{{ errors.first('address_id') }}</span>
                                <el-button v-loading.fullscreen.lock="fullscreenLoading" v-on:click="addAddress()"
                                           size="small" round type="success">افزودن آدرس <span
                                            class="fa fa-map-marker"></span></el-button>
                            </dd>
                        </dl>
                    </div>

                    <hr>

                    <div class="row">
                        <h4 class="heading-order-part">لیست سفارشات</h4>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th> محصول</th>
                                    <th>قیمت واحد</th>
                                    <th>تعداد</th>
                                    <th>قیمت کل</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(product,index) in cartProducts">
                                    <td v-text="index+1"></td>
                                    <td>
                                        <div style="display: flex;align-items: center;">
                                        <span style="margin-left: 10px"><img :src="'/images/products/tn-'+product.image"
                                                                             class="img-thumbnail"
                                                                             alt="" width="70" height="70"></span>
                                            <a href=""
                                               style="display: flex;align-items: right;flex-direction: column;">
                                                <span v-text="product.name"></span>
                                                <span v-text="product.amount"></span>
                                            </a>
                                        </div>
                                    </td>
                                    <td v-text="formatPrice(product.price)"></td>
                                    <td>
                                        <el-input-number
                                                v-model="orderInfo.numberCartProducts[index]"
                                                size="mini"
                                                :min="convertToInt(product.min_order)"
                                                :max="convertToInt(product.quantity)">
                                        </el-input-number>
                                    </td>
                                    <td v-text="formatPrice(product.price*orderInfo.numberCartProducts[index])"></td>
                                </tr>
                                <tr>
                                    <td colspan="4">قیمت کل</td>
                                    <td v-text="totalPrice()"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <h4 class="heading-order-part">هزینه ارسال: &nbsp;&nbsp;0 تومان</h4>

                    </div>
                    <hr>

                    <div class="row">
                        <h4 class="heading-order-part">میخواهید توضیحی درباره سفارش دهید؟</h4>
                        <br>
                        <el-form size="mini">
                            <el-form-item :class="{'input-empty': errors.has('description') }" label="">
                                <el-input type="textarea" name="description" data-vv-as="توضیحات"
                                          v-validate="'min:10'"
                                          v-model="orderInfo.description" placeholder="توضیحات"></el-input>
                                <span v-show="errors.has('description')">@{{ errors.first('description') }}</span>
                            </el-form-item>
                        </el-form>
                    </div>
                    <br>
                    <hr>
                    <br>

                    <div class="row">

                        <h4 class="heading-order-part">نحوه پرداخت</h4>

                        <div style="margin-top: 20px">
                            <el-radio-group v-model="orderInfo.payment" size="small">
                                <el-radio label="at_place" border>پرداخت در محل</el-radio>
                                &nbsp
                                <el-radio label="online" disabled border>پرداخت آنلاین</el-radio>
                            </el-radio-group>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <form action="/v1/orders/store" method="POST">
                            {{ csrf_field() }}
                            <input name="orderInfo" type="hidden" :value="JSON.stringify(orderInfo)">

                            <el-button
                                    :disabled="isOrderComplete"
                                    type="success" native-type="submit" plain>ثبت سفارش
                            </el-button>
                        </form>
                    </div>
                </el-card>
            </div>
        </section>

        <el-dialog :fullscreen="true" :title="address.title" :visible.sync="address.visible">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <el-form label-position="top" size="small">
                    <el-form-item :class="{'input-empty': errors.has('province') }" label="استان :">
                        <el-select name="province" :default-first-option="true" data-vv-as="استان"
                                   v-validate="'required'" v-on:change="getCities()"
                                   v-model="address.data.province"
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
                        <span v-show="errors.has('province')">@{{ errors.first('province') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('city') }" label="شهر :">
                        <el-select name="city" data-vv-as="شهر" v-validate="'required'"
                                   v-model="address.data.city"
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
                        <span v-show="errors.has('city')">@{{ errors.first('city') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('local') }" label="منطقه :">
                        <el-input name="local" data-vv-as="منطقه" v-validate="'min:5'"
                                  v-model="address.data.local"></el-input>
                        <span v-show="errors.has('local')">@{{ errors.first('local') }}</span>
                    </el-form-item>
                    <el-form-item :class="{'input-empty': errors.has('address') }" label="آدرس :">
                        <el-input name="address" data-vv-as="آدرس" v-validate="'required|min:5'"
                                  v-model="address.data.address"></el-input>
                        <span v-show="errors.has('address')">@{{ errors.first('address') }}</span>
                    </el-form-item>
                    <br>
                    <el-form-item>
                        <div class="form-buttons">
                            <el-button v-on:click="saveAddress()" size="small" round type="success">ثبت آدرس<i
                                        class="el-icon-arrow-left el-icon-right"></i></el-button>
                        </div>
                    </el-form-item>
                </el-form>
            </div>
        </el-dialog>
    </div>
@endsection