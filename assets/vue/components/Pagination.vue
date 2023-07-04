<template>
  <div class="table-pagination">
      <select id="pageSize" style="margin-right: 1rem;" v-model="filterMode.itemsPerPage" @change="changeItemsPerPage(null)"   >
        <option value="25">25</option>
        <option value="50" selected>50</option>
        <option value="75">75</option>
        <option value="100">100</option>
      </select>
          <a class="pagination-item"
            @click="changeItemsPerPage(index+1)"
            :class="{'selected' : (index + 1) === filterMode.page}"
            v-for="(item, index) in pokemonStore.links" :key="index"
            href="#">
            {{ index + 1 }}
          </a>
  </div>
</template>


<script setup>
import {ref, watch} from 'vue';
import { usePokemonStore } from '../stores/pokemons';
const pokemonStore = usePokemonStore();

const filterMode = ref(pokemonStore.filterParams)

watch(() => pokemonStore.response, (val) => {
}, {deep: true})


function changeItemsPerPage(page = null) {
  if (page) {
    pokemonStore.filterParams.page = page;
  }

  const params = {
    ...pokemonStore.filterParams
  }
  pokemonStore.reloadData('?' +new URLSearchParams(params).toString())
}

</script>

