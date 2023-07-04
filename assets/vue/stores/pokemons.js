import { defineStore } from 'pinia'
import { ref } from 'vue'
import swal from 'sweetalert';
import {  useRouter  } from "vue-router";

export const usePokemonStore = defineStore('pokemon', () => {
  const currentPage = ref(1)
  const pokemons = ref([])
  const response = ref(null);
  const isLoading = ref(true);


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

  const itemsPerPage = ref(50)
  const links = ref(0)
  function updatePagination(){
    const totalItems = response.value["totalItems"]
    const urlParams = new URLSearchParams(response.value['url'].replace('/api/pokemon', ''));
    const itemsPerPage = urlParams.get('itemsPerPage') ?? 50
    links.value = Math.ceil(totalItems / itemsPerPage);
  }
  reloadData()
const router = useRouter(); 
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
      console.log(val)
    });
  }


  const pokemonTypes = ref([])
  fetch(`/api/pokemon_types?page=1`)
  .then(res => res.json())
  .then(typeList => {
    pokemonTypes.value = typeList["hydra:member"];
  })

  const filterParams = ref({
    name: '',
    legendary: '',
    type: '',
    generation: '',
    itemsPerPage: 50,
    page: 1
  })



  return { pokemons, currentPage, response, isLoading, reloadData, itemsPerPage, links, showLoginModal, pokemonTypes, filterParams }
})