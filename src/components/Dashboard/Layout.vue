<template>
    <div class="container-fluid">
        <top-navbar></top-navbar>
        <div class="row">
            <side-menu></side-menu>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <router-view></router-view>
                </div>
            </main>
        </div>
    </div>
</template>

<script>

    import TopNavbar from './TopNavbar.vue'
    import SideMenu from './SideMenu.vue'

    export default {
        name: 'DashboardLayout',
        components: {
            TopNavbar,
            SideMenu
        },
        data () {
            return {
                msg: 'Welcome to Your Vue.js App'
            }
        }
    }
</script>

<style lang="scss">
    body {
        font-size: .875rem;
    }

    [role="main"] {
        padding-top: 133px; /* Space for fixed navbar */
    }

    @media (min-width: 768px) {
        [role="main"] {
            padding-top: 48px; /* Space for fixed navbar */
        }
    }
</style>