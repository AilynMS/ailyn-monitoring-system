<template>
  <div class="wrapper">
    <button 
      v-if="$auth.user"
      @click="$router.push(`/${$auth.user.tenant.slug}/verificaciones/web`);">
      Redirect
    </button>

    <button v-else
      @click="$router.push('/auth/login');">
      Redirect     
    </button>
  </div>
</template>

<script>
export default {
  created() {
    if(this.$route.query.verified) this.$router.push('/auth/login?verified=true');
    if(this.$route.query.success) this.$router.push('/auth/login?success=true');
  }
}
</script>