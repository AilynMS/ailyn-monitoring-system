<template>
   <section class="empty-wrapper text-center w-full pt-10 pb-10">
      <img 
         v-if="showImage"
         loading="lazy" 
         class="mx-auto" 
         width="150" 
         src="/ux/empty.svg" 
         alt="">

      <h1 class="mt-4 md:text-3xl text-xl font-bold text-gray-700">
         {{ title ? title : `Aún no tienes ninguna verificación ${type ? type : ''}`}}
      </h1>
      
      <p class="description text-gray-600 mb-5">{{ subtitle ? subtitle : '¡Comienza añadiendo una!'}}</p>

      <slot></slot>
   </section>
</template>

<script>
export default {
   name: 'empty-screen',

   props: {
      title: {
         required: false
      },
      subtitle: {
         required: false
      },
      showImage: {
         required: false,
         default: true
      },
      type: {
         required: false
      }
   }
}
</script>