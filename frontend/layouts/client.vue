<template>
  <main class="">
    <Navigation />
    
    <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8 mb-6">
      <Nuxt />
    </div>
  </main>
</template>

<script>
import Navigation from '~/components/shared/navigation/Navigation.vue';

export default {
  components: {
    Navigation
  }
}
</script>