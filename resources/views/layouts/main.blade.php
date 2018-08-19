<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>بانپ</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/vendor.css">

    <!-- Scripts -->
    <script>

        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'user' => Auth::user(),
            'auth' => Auth::check(),
        ]) !!};
    </script>
</head>
<body>
<div id="app">
    @if (session('paymentF'))
        <input type="hidden" id="paymentMessageInput" value="{{session('paymentF')}}">
        <input type="hidden" id="paymentStatusInput" value="error">
        @{{ paymentMsg() }}
    @endif
    @if (session('paymentS'))
        <input type="hidden" id="paymentMessageInput" value="{{session('paymentS')}}">
        <input type="hidden" id="paymentStatusInput" value="success">
        @{{ paymentMsg() }}
    @endif
    <header>
        <div class=" hidden-sm hidden-xs" style="background-color: rgb(10, 151, 202) ; color:white ;" class="top-bar">
            <div class="container">
                <span class="header-date" v-text="jdate"></span>

                <div class="login-regester-header">
                        <span v-if="!auth">
                            <router-link class="btn" :to="{name : 'pages.login'}">ورود</router-link>
                        <router-link class="btn" :to="{name : 'pages.register'}">ثبت نام</router-link>
                            </span>
                    <a v-if="auth" href="/dashboard" class="btn"><span class="fa fa-dashboard"></span> پنل کاربر</a>
                    <span v-if="auth">
                        <a style="text-align: right" class="btn" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <span class="fa fa-sign-out"></span> خروج
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </span>
                </div>
            </div>
        </div>


        <div class=" hidden-sm hidden-xs" style="background-color: #004bbe;color:white;padding: 5px 0">
            <div class="container">
                <div class=" header-main">
                    <div class="header-cart">
                        <a :href="auth ? '/checkout' : '/login'">
                            <el-button plain size="mini">
                                <span class="fa fa-shopping-cart"></span>&nbsp;&nbsp;
                                <span class="text-cart"> سفارشات</span>
                                <el-tag type="danger" size="mini" v-text="cartNumber"></el-tag>
                            </el-button>
                        </a>
                    </div>
                    <div class="header-search">
                        <form v-on:submit.prevent="searchProduct()">
                            <el-input placeholder="جستجوی محصول..." name="search" v-model="filters.search">
                                <el-button type="info" native-type="submit" slot="append"
                                           icon="el-icon-search"></el-button>
                            </el-input>
                        </form>
                    </div>
                    <div class="header-logo">
                        <a href="/">
                            <img class="img-responsive" :src="settings['site-logo'].value" alt="" width="100"
                                 height="100">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="line"></div>
        <div style="background-color: #0d408d;color: white;padding: 0">
            <div class="container header-menu">
                <mega-menu></mega-menu>
            </div>
        </div>
    </header>

    <section>
        @yield('content')
    </section>

    <footer>
        <div class="social-networks">
            <div class="container">
                <a :href="settings['telegram-link'].value"><span class="fa fa-telegram"></span></a>
                <a :href="settings['instagram-link'].value"><span class="fa fa-instagram"></span></a>
                <a :href="settings['twitter-link'].value"><span class="fa fa-twitter"></span></a>
                {{--<a href="#"><span class="fa fa-facebook"></span></a>--}}
                {{--<a href="#"><span class="fa fa-youtube"></span></a>--}}
            </div>
        </div>
        <div class="contact-info">
            <div class="container">
                <div class="row ">
                    <div class="col-sm-4 right-info">
                        <a :href="settings['contact-us-link'].value"><span class="fa fa-phone"></span>&nbsp; تماس با ما
                        </a>
                        <a :href="settings['law-link'].value"><span class="fa fa-balance-scale"></span>&nbsp; قوانین
                        </a>
                        <a :href="settings['about-us-link'].value"><span class="fa fa-users"></span>&nbsp; درباره ما
                        </a>
                        <a :href="settings['questions-link'].value"><span class="fa fa-question-circle-o"></span>&nbsp;
                            سوالات متداول </a>
                    </div>
                    <div class="col-sm-4 center-info">
                        <h2>پشتیبانی سایت</h2>
                        <h2 v-text="settings['support-phone'].value"></h2>
                    </div>
                    <div class="col-sm-4 left-info">
                        <div class="row">
                            {{--<div class="col-md-6"><a :href="settings['enamad-link'].value"><img class="img-responsive" src="/images/footer/logo.png"--}}
                            {{--width="110" height="110" alt=""></a></div>--}}
                            <div class="col-md-6"><a :href="settings['senfi-link'].value"><img class="img-responsive"
                                                                                               src="/images/footer/senfi.png"
                                                                                               width="110" height="110"
                                                                                               alt=""></a></div>
                        </div>
                        <div class="row">
                            {{--<div class="col-md-6"><a :href="settings['shaparak-link'].value"><img class="img-responsive" src="/images/footer/shaparak.png"--}}
                            {{--width="110" height="110" alt=""></a></div>--}}
                            <div class="col-md-6"><a :href="settings['samandehi-link'].value"><img
                                            class="img-responsive" src="/images/footer/neshan.png"
                                            width="110" height="110" alt=""></a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <span>کلیه حقوق این سایت برای آسا محفوظ می باشد.</span>
                <span style="font-size:x-small">Copyright asa © 2016 - 2017 B.41 </span>
            </div>
        </div>
    </footer>

    <section class="fix-footer  navbar navbar-primary navbar-fixed-bottom hidden-sm hidden-md hidden-lg">
        <div class="container">
            <a :href="auth ? '/checkout' : '/login'">
                <el-button size="small" plain>بررسی وثبت سفارش</el-button>
            </a>
            <div class="pull-left">
                <el-tag type="danger" v-text="cartNumber"></el-tag>
            </div>
        </div>
    </section>
    <el-dialog v-if="settings['health-dialog-status'].value == 1"
               title="کد سلامت محصولات"
               :visible.sync="health_dialog"
               width="50%"
               center>
        <a :href="settings['health-page-link'].value" class="text-center"><h2>صفحه کد سلامت محصولات</h2></a>
    </el-dialog>

</div>
</div>

<script src="/js/app.js"></script>
<script>
    $(document).ready(function () {
        $(".dropdown").hover(
            function () {
                $('.dropdown-menu', this).stop(true, true).slideDown("fast");
                $(this).toggleClass('open');
            },
            function () {
                $('.dropdown-menu', this).stop(true, true).slideUp("fast");
                $(this).toggleClass('open');
            }
        );
    });

    function flyToElement(flyer, flyingTo) {
        var $func = $(this);
        var divider = 3;
        var flyerClone = $(flyer).clone();
        $(flyerClone).css({
            position: 'absolute',
            top: $(flyer).offset().top + "px",
            left: $(flyer).offset().left + "px",
            opacity: 1,
            'z-index': 1000
        });
        $('body').append($(flyerClone));
        var gotoX = $(flyingTo).offset().left + ($(flyingTo).width() / 2) - ($(flyer).width() / divider) / 2;
        var gotoY = $(flyingTo).offset().top + ($(flyingTo).height() / 2) - ($(flyer).height() / divider) / 2;

        $(flyerClone).animate({
                opacity: 0.4,
                left: gotoX,
                top: gotoY,
                width: $(flyer).width() / divider,
                height: $(flyer).height() / divider
            }, 700,
            function () {
                $(flyingTo).fadeOut('fast', function () {
                    $(flyingTo).fadeIn('fast', function () {
                        $(flyerClone).fadeOut('fast', function () {
                            $(flyerClone).remove();
                        });
                    });
                });
            });
    }

    $(document).ready(function () {
    });
</script>
</body>
</html>
