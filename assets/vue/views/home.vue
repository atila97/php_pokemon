<template>
  <RouterView />
  <div class="pokemons-table-list">
    <div class="table-filter">
      <div class="table-filter-row">
        <div class="search-input">
          <div>
            <label for="filter_name">Name</label>
            <input type="text" v-model="filterMode.name" class="search-input" />
          </div>
        </div>
        <div class="select-filter">
          <div>
            <label for="filter_legendary" class="required">Legendary</label>
            <select id="filter_legendary" v-model="filterMode.legendary" >
              <option value="0" selected="selected">reset</option>
              <option value="1">Yes</option>
              <option value="2">No</option>
            </select>
          </div>
        </div>
        <div class="select-filter">
          <div>
            <label for="filter_type">Type</label>
            <select id="filter_type" v-model="filterMode.type">
              <option value="">Tous les pokemons</option>
              <option :value="item['@id']" v-for="(item, index) in pokemonStore.pokemonTypes" :key="index">
                {{item.name}}
              </option>
            </select>
          </div>
        </div>
  
        <div class="select-filter">
          <div>
            <label for="filter_generation">Generation</label>
            <input type="text" id="filter_generation" v-model="filterMode.generation"  />
          </div>
        </div>
  
        <div class="btn-apply">
          <button @click="handleFilter">
              Rechercher
            </button>
        </div>
      </div>
    </div>
    <div class="table-filter">
      <div class="table-content">
        <div class="table-header">
          <span class="col-item">ID</span>
          <span class="col-item col-name">Name</span>
          <span class="col-item">Total</span>
          <span class="col-item col-type">Types</span>
          <span class="col-item">HP</span>
          <span class="col-item">Generation</span>
          <span class="col-item">Legendary</span>
        </div>

        <div class="table-body">
          <PokemonRow :pokemon="item" v-for="(item, index) in pokemonStore.pokemons" :key="index" />
        </div>
      </div>
    </div>

    <Pagination />
  </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from "vue";
import { usePokemonStore } from "../stores/pokemons";
import PokemonRow from "../components/pokemonRow.vue";
import Pagination from "../components/Pagination.vue";
const pokemonStore = usePokemonStore();


const filterMode = ref(pokemonStore.filterParams)


function handleFilter() {
  const params = {
    ...filterMode.value,
    page: 1
  }
  pokemonStore.reloadData('?' +new URLSearchParams(params).toString())
}
</script>
