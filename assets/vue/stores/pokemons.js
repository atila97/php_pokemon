import { defineStore } from 'pinia'
import { ref } from 'vue'
import swal from 'sweetalert';
import {  useRouter  } from "vue-router";

export const usePokemonStore = defineStore('pokemon', () => {
  const pokemons = ref([])
  const response = ref(null);
  const isLoading = ref(true);
  const pokemonTypes = ref([])
  const links = ref(0)
  const router = useRouter(); 

  const filterParams = ref({
    name: '',
    legendary: '',
    type: '',
    generation: '',
    itemsPerPage: 50,
    page: 1
  })

  const reloadData = (params = '?page=1') => {
    fetch(`/api/pokemon${params}`)
    .then(res => res.json())
    .then(list => {
      response.value = {
        "totalItems": list["hydra:totalItems"],
        "url" :  list['hydra:view']["@id"]
      }
      pokemons.value = list["hydra:member"].map(item => {
        return {
          ...item,
          total: item['hp'] + item["attack"] + item['defense'] + item['spAtk'] + item["spDef"] + item["speed"]
        }
      })
      isLoading.value = false;
      updatePagination()
    })
  }

  
  function updatePagination(){
    const totalItems = response.value["totalItems"]
    const urlParams = new URLSearchParams(response.value['url'].replace('/api/pokemon', ''));
    const itemsPerPage = urlParams.get('itemsPerPage') ?? 50
    links.value = Math.ceil(totalItems / itemsPerPage);
  }
  
  const showLoginModal = () => {
    swal({
      title: "Authentification requise.",
      text: "Il n'est pas possible de modifier ni supprimer. ",
      icon: "error",
      buttons: ["Annuler", "Me connecter"],
      dangerMode: true,
    }).then(val => {
      if (null === val) return;
      router.push({name: 'login'})
    });
  }
  
  fetch(`/api/pokemon_types?page=1`)
  .then(res => res.json())
  .then(typeList => {
    pokemonTypes.value = typeList["hydra:member"];
  })


  reloadData()
  return { pokemons, response, isLoading, reloadData, links, showLoginModal, pokemonTypes, filterParams }
})