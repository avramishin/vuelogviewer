import axios from 'axios'
export default {
  install(Vue, options) {
    const instance = axios.create(options);
    Vue.prototype.$api = instance;
  }
}
