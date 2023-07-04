<template>
  <div class="table-row">
    <div class="col-item">
      <router-link
        :to="{ name: 'pokemonDetails', params: { id: pokemon.id } }"
        class="btn-show"
        >
        {{ pokemon.id }}
      </router-link>
    </div>
    <div class="col-item col-name">
      {{ pokemon.name }}
    </div>
    <div class="col-item">
      {{ pokemon.total }}
    </div>
    <div class="col-item col-type">
      <span class="pokemon-type" v-for="(pokemonType, index) in pokemon.type" :key="index">
        {{ pokemonType.name }}
      </span>
    </div>
    <div class="col-item">
      {{ pokemon.hp }}
    </div>
    <div class="col-item">
      {{ pokemon.generation }}
    </div>
    <div class="col-item">
      {{ pokemon.legendary ? "Oui" : "Non" }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  pokemon: {
    required: true,
  },
});
</script>
