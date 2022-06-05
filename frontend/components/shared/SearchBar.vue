<template>
  <section 
    ref="searchComponent"
    class="flex bg-white rounded-md shadow-sm flex mb-4 border-2 border-gray-300 overflow-hidden">
    
    <div 
      @click="$refs.input.focus()" 
      class="left my-auto px-3 text-gray-500 cursor-text">
      <fa icon="search" />
    </div>
    
    <input 
      @focus="addClass"
      @blur="removeClass"
      @keyup="handleSearch"
      v-model="searchText"
      ref="input"
      type="text" 
      class="w-full rounded p-2 outline-none text" 
      placeholder="Ingresa un texto para buscar">
  </section>
</template>

<script>
/* 
    Events: 'onsearch'
*/

export default {
  name: 'SearchBar',

  data() {
    return {
      searchText: null
    }
  },

  methods: {
    handleSearch() {
      return this.$emit('onSearch', this.searchText);
    },

    addClass() {
      return this.$refs.searchComponent.classList.add('primary-shadow');
    },

    removeClass() {
      return this.$refs.searchComponent.classList.remove('primary-shadow');
    }
  }
}
</script>