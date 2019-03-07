<template>
  <div>
    <b-container fluid>
      <div class="login-wrapper">
        <b-card header="Authentication">
          <b-card-body>
            <b-form @submit.prevent="login">
              <b-form-group label="Username" label-for="username">
                <b-form-input id="username" type="text" v-model="username"/>
              </b-form-group>
              <b-form-group label="Password" label-for="password">
                <b-form-input id="password" type="password" v-model="password"/>
              </b-form-group>
              <b-alert fade dismissible variant="danger" v-model="alert.dismiss">{{alert.message}}</b-alert>
              <div class="d-flex justify-content-end">
                <div>
                  <b-button type="submit" variant="primary" class="w-100">Login</b-button>
                </div>
              </div>
            </b-form>
          </b-card-body>
        </b-card>
      </div>
    </b-container>
  </div>
</template>

<script>
export default {
  methods: {
    login() {
      let data = new FormData();

      data.append("username", this.username);
      data.append("password", this.password);
      data.append("action", "login");

      this.$api({
        method: "POST",
        url: "/",
        data: data
      }).then(response => {
        if (response.data.error) {
          this.alert.message = response.data.error;
          this.alert.dismiss = 5;
        } else {
          this.$router.push({ path: "/" });
        }
      });
    }
  },
  data() {
    return {
      username: "",
      password: "",
      alert: {
        message: "",
        dismiss: 0
      }
    };
  }
};
</script>

<style>
.login-wrapper {
  display: flex;
  justify-content: center;
  height: 80vh;
  align-items: center;
}

.login-wrapper .card {
  width: 500px;
}
</style>
