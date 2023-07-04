<template>

<div id="loginContent">
  <h2>Bienvenue <small>Pokemon</small></h2>
  <span>Formulaire de connexion</span>
    <form action="#" method="post" class="login-form">
      <label for="username">E-mail:</label>
      <input type="text" id="username" name="_username" v-model="login.email" >

      <label for="password">Mot de passe:</label>
      <input type="password" id="password" name="_password" v-model="login.password">

      <button type="button" @click="handleLogin">Connexion</button>
  </form>

  <span class="register-span">
      Vous n'avez pas de compte?
      <router-link :to="{name: 'register'}">Créer compte</router-link>
  </span>
</div>


</template>

<script setup>
import {ref} from 'vue';
import { useRoute, useRouter  } from "vue-router";
import swal from 'sweetalert';
const router = useRouter();


const login = ref({email: "", password: ''})

const handleLogin = () => {
  fetch(`/api/login`, {
    method: 'POST',
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      ...login.value
    })
  }).then(res => res.json())
    .then(data =>  {
      if (data['code'] === 401) {
        swal({
          title: "Identifiant invalide.",
          text: " Email ou mot de passe incorrect",
          icon: "error",
        })
        return;
      }
      localStorage.setItem("authToken", data["token"])
      router.push({name: 'home'})
    })
}
</script>