<template>
    <div id="app">
        <div class="login-wrapper">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    export default {
        components: {},
        name: 'minimal-layout'
    }
</script>

<style lang="scss">
    @import "../../../sass/partials/_vars.scss";
    @import "../../../sass/partials/_mixins.scss";
    @import "../../../sass/partials/_shared.scss";

    #mainHeader {
        height: $headerHeight;
        background: $color2ndBgr;
        display: flex;
        border-bottom: 1px solid $colorMainBgr;

        h1.brand {
            flex: 1;
            color: $colorMainText;
            font-size: 1.7rem;
            font-weight: $fontWeight_UltraThin;
            opacity: 0;
            line-height: $headerHeight;
            text-align: center;
        }

        .hamburger, .magnifier {
            font-size: 1.4rem;
            flex: 0 0 48px;
            order: -1;
            line-height: $headerHeight;
            text-align: center;
            display: none;
        }

        @media only screen and (max-width: 667px) {
            display: flex;
            align-content: stretch;
            justify-content: flext-start;

            .hamburger, .magnifier {
                display: inline-block;
            }

            h1.brand {
                opacity: 1;
            }
        }
    }

    /**
     * I like to move it move it
     * I like to move it move it
     * I like to move it move it
     * You like to - move it!
     */
    @keyframes shake {
        8%, 41% {
            -webkit-transform: translateX(-10px);
        }
        25%, 58% {
            -webkit-transform: translateX(10px);
        }
        75% {
            -webkit-transform: translateX(-5px);
        }
        92% {
            -webkit-transform: translateX(5px);
        }
        0%, 100% {
            -webkit-transform: translateX(0);
        }
    }

    form {
        width: 280px;
        padding: 1.8rem;
        background: rgba(255,255,255,.08);
        border-radius: .6rem;
        border: 1px solid #333;

        &.error {
            border-color: #8e4947;
            animation: shake .5s;
        }

        &::before {
            content: " ";
            display: block;
            background: url(/img/logo.svg) center top no-repeat;
            background-size: 156px;
            height: 172px;
        }

        @media only screen and (max-width : 414px) {
            border: 0;
            background: transparent;
        }
    }

    input {
        display: block;
        margin-top: 12px;
        border: 0;
        background: #fff;
        outline: none;
        width: 100%;
    }

    button {
        display: block;
        margin-top: 12px;
        width: 100%;
    }
</style>
