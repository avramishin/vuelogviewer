<template>
  <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">Vue Log Viewer</a>
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <a class="nav-link" href="#" @click="logout">Sign out</a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  methods: {
    logout() {
      this.$api({
        method: "GET",
        url: "/",
        params: {
          action: "logout"
        }
      }).then(response => {
        this.$router.push({ path: "/login" });
      });
    }
  },
  data() {
    return {
      msg: ""
    };
  }
};
</script>

<style lang="scss">
.navbar-brand {
  padding-top: 0.75rem;
  padding-bottom: 0.75rem;
  font-size: 1rem;
  background-color: rgba(0, 0, 0, 0.25);
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, 0.25);
}
</style>