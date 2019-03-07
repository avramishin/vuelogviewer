import Vue from "vue";
import BootstrapVue from "bootstrap-vue";
import App from "./App.vue";
import VueRouter from "vue-router";
import EvaIcons from "vue-eva-icons";
import Api from './plugins/Api.js'
import TreeView from "vue-json-tree-view"

import "./assets/css/simplex/bootstrap.min.css";
//import "./assets/css/sketchy/bootstrap.min.css"
import "bootstrap-vue/dist/bootstrap-vue.css";

Vue.use(BootstrapVue);
Vue.use(VueRouter);
Vue.use(EvaIcons);
Vue.use(TreeView);

Vue.use(Api, {
    baseURL: api_base_url,
    withCredentials: true
})

import Dashboard from "./components/Dashboard/Layout.vue";
import Home from "./components/Dashboard/pages/Default.vue";
import Login from "./components/Dashboard/pages/Login.vue";

const router = new VueRouter({
    routes: [{
        path: "/",
        component: Dashboard,
        children: [{
            path: "/",
            component: Home
        }]
    }, {
        path: "/login",
        component: Login
    }]
});


Vue.prototype.$api.interceptors.response.use(
    response => response,
    error => {
        if (error.response.status === 401) {
            router.push("/login");
        }

        return Promise.reject(error.response.data.error);
    }
);


new Vue({
    el: "#app",
    router: router,
    render: h => h(App)
});