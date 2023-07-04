<template>
  <div class="pokemon-info" v-if="pokemon">
    <span class="pokemon-name">
      {{ pokemon.number }} - {{ pokemon.name }}
    </span>

    <div class="pokemon-type-list">
      <span class="pokemon-type"  v-for="(pokemonType, index) in pokemon.type" :key="index">{{ pokemonType.name }}</span>
      <span class="pokemon-generation" >Génération {{ pokemon.generation }}</span>
      <span class="pokemon-leng" v-if="pokemon.legendary">Pokémon légendaire</span>
    </div>

    <!-- {% for type, messages in app.flashes %} {% for message in messages %}
    <div class="alert alert-{{ type }}">
      {{ message }}
    </div>
    {% endfor %} {% endfor %} -->

    <div class="pokemon-stats">
      <span class="stat-title">Statistique de base</span>
      
      <StatRow name="HP" :value="pokemon.hp" />
      <StatRow name="Attaque" :value="pokemon.attack" />
      <StatRow name="Défense" :value="pokemon.defense" />
      <StatRow name="Atq Spé" :value="pokemon.spAtk" />
      <StatRow name="Déf Spé" :value="pokemon.spDef" />
      <StatRow name="Vitesse" :value="pokemon.speed" />
      <div class="stat-row no-border">
        <span class="stat-name">Total</span>
        <span class="stat-value">{{ pokemon.total }}</span>
      </div>
    </div>

    <div class="buttons-action">
      <router-link
        :to="{ name: 'pokemonEdit', params: { id: pokemon.id } }"
        class="btn-modify">
        Modifier
      </router-link>
      <button class="btn-delete" @click="handleDelete">Supprimer</button>
    </div>

    <router-link
      :to="{ name: 'home'}"
      class="btn-show">
        Retourner à la liste
      </router-link>
  </div>
  <div v-else>
    loading
  </div>
</template>

<script setup>
import { ref } from 'vue'
import StatRow from '../components/StatRow.vue';
import { useRoute, useRouter  } from "vue-router";
import swal from 'sweetalert';
import { usePokemonStore } from "../stores/pokemons";
const pokemonStore = usePokemonStore();

const router = useRouter();
const pokemon = ref(null)
const route = useRoute();
fetch(`/api/pokemon/${route.params.id}`).then(res => res.json())
.then(res => {
  console.log(res)
  pokemon.value = {
    ...res,
    total: res['hp'] + res["attack"] + res['defense'] + res['spAtk'] + res["spDef"] + res["speed"]
  }
})


function handleDelete() {
  swal({
    title: "Êtes-vous sûr?",
    text: "Une fois supprimé, vous ne pourrez plus récupérer ce pokemon.",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (!willDelete) return;

    fetch(`/api/pokemon/${pokemon.value.id}`, {
      method: "DELETE",
      headers: {
        'Authorization': `Bearer ${localStorage.getItem("authToken")}`
      },
    })
      .then(res=>res.json())
      .then(data => {
        if (data.code === 401) {
          pokemonStore.showLoginModal()
          return;
        }

        if (data['status'] !== "OK") {
          swal({
            title: "Erreur",
            text: data["message"],
            icon: "error",
          })
          return;
        }
        router.push({ name: 'home' })
      })

  });

}
</script>
