<template>
  <div id="registerContent">
    <h1>Register</h1>



    <form name="registration_form" method="#" class="login-form">
      <div>
        <label for="registration_form_email" class="required">Email</label>
        <input type="text" required="required" maxlength="100" v-model="register.email">
      </div>
      <div>
        <label for="registration_form_plainPassword" class="required">Password</label>
        <input type="password" v-model="register.password" required="required" autocomplete="new-password">
      </div>

      <div class="alert alert-danger" role="alert" v-if="errors.length > 0" style="width: 100%;">
        <li v-for="(item, index) in errors" :key="index">{{ item }}</li>
      </div>
      <button type="button" class="btn" @click="handleRegister">Register</button>
    </form>
    <span class="register-span">
      Vous avez une compte ?
      <router-link :to="{name: 'login'}" >Connexion</router-link>
    </span>
  </div>
</template>

<script setup>
import {ref} from 'vue'
import { useRouter  } from "vue-router";
const router = useRouter();



const register = ref({email: "", password: ''})


const errors = ref([]);

function handleRegister() {
  errors.value = [];
  if (!validateEmail(register.value.email)) {
    errors.value.push("L'e-mail n'est pas valide.")
  }

  if (!validatePassword(register.value.password)) {
    errors.value.push(" Le mot de passe doit comporter au minimum 8 caractères, une majuscule, une minuscule et un caractère spécial ,;:?./@#'{[]}()$*%=+ ")
  }

  if (errors.value.length === 0) {

    fetch('/api/register', {
      method: 'POST',
      body: JSON.stringify({...register.value})
    }).then(res => res.json())
      .then(data => {
        if (data["errors"]) {
          errors.value = data["errors"];
          return;
        }
        router.push({name: 'login'})
      })
  }
}


function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function validatePassword(password) {
  const minLength = 8;
  const hasSpecialChar = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]+/;
  return password.length >= minLength && hasSpecialChar.test(password);
}

</script>