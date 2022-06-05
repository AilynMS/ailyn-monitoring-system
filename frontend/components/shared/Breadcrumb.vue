<template>
   <section class="breadcrumb-wrapper mb-4 w-full">
      <nav class="text-gray-700 font-medium text-xs md:text-sm" aria-label="Breadcrumb">

         <ol class="list-none p-0 inline-flex">
            <li class="flex items-center">
               <a class="font-medium">
                  {{ $auth.user ? $auth.user.tenant.name : 'AilynMS' }}
               </a>
            </li>
            
            <template v-if="urls">
               <li 
                  class="flex" 
                  v-for="(url, index) in urls" 
                  :key="index + '_url'">

                  <svg class="my-auto fill-current w-3 h-3 mx-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                  
                  <nuxt-link 
                     :to="`${domain}/${url.path}`" 
                     :class="url.last ? 'text-gray-500' : ''" 
                     aria-current="page">
                     {{ url.name }}
                  </nuxt-link>
               </li>
            </template>
         </ol>

      </nav>
   </section>
</template>

<script>
export default {
   name: 'breadcrumb',
   
   props: {
      urls: {
         required: false,
         type: Array
      }
   },

   data() {
      return {
         domain: this.$auth.user ? `/${this.$auth.user.tenant.slug}/verificaciones` : '/ailynms'
      }
   },


}
</script>