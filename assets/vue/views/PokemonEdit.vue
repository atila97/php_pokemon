<template>
  <div class="pokemon-info" v-if="pokemon">
    <h3>Formulaire d'édition</h3>
    <form name="pokemon_form">
      <div id="pokemon_form">
        <div>
          <label class="required">Name</label>
          <input type="text"  required="required" maxlength="100" v-model="pokemon.name" />
        </div>
        <div>
          <label>Legendary</label>
          <input type="checkbox" v-model="pokemon.legendary" />
        </div>
        <div>
          <label class="required">Generation</label>
          <input type="number"  required="required" v-model="pokemon.generation" />
        </div>
        <div>
          <label>Type1</label>
          <select required="required" v-model="pokemon['type1']">
            <option value="">---</option>
            <option :value="item" v-for="(item, index) in pokemonStore.pokemonTypes" :key="index">
              {{item.name}}
            </option>
          </select>
        </div>
        <div>
          <label for="pokemon_form_Type2">Type2</label>
          <select v-model="pokemon['type2']">
            <option value="">---</option>
            <option :value="item" v-for="(item, index) in pokemonTypes" :key="index">{{item.name}}</option>
          </select>
        </div>
      </div>
    </form>

    <div>

      <div class="buttons-action">
        <router-link
          :to="{ name: 'pokemonDetails', params: { id: pokemon.id } }"
          class="btn-modify">
          Annuler
        </router-link>

        <a href="#" type="button" class="btn-delete" @click="udpateAction">
          Mettre à jour
        </a>
      </div>



      <router-link :to="{ name: 'home' }" class="btn-show">
        Retourner à la liste
      </router-link>

    </div>

  </div>
  <div v-else>loading</div>
</template>

<script setup>
import { ref } from "vue";
import StatRow from "../components/StatRow.vue";
import { useRoute, useRouter } from "vue-router";
import swal from 'sweetalert';
import { usePokemonStore } from "../stores/pokemons";
const pokemonStore = usePokemonStore();

const router = useRouter();

const pokemon = ref(null);
const route = useRoute();
fetch(`/api/pokemon/${route.params.id}`)
  .then((res) => res.json())
  .then((res) => {
    pokemon.value = res;
    pokemon.value['type1'] = res['type'][0]
    pokemon.value['type2'] = res['type'][1] 
  });

const udpateAction = () => {
  const data = {
    ...pokemon.value,
    type1: pokemon.value["type1"] ? pokemon.value["type1"]['id'] : null,
    type2: pokemon.value["type2"] ? pokemon.value["type2"]['id'] : null,
    type: null
  }

  fetch(`/api/pokemon/${pokemon.value.id}`, {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${localStorage.getItem("authToken")}`
    },
    body: JSON.stringify(data)
  })
    .then(res => res.json())
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

      router.push({ name: 'pokemonDetails', params: { id: pokemon.value.id } })
      console.log(data)
    })
}

</script>
