<template>
      <AmsRow>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
        <AmsColumn><p class="skeleton px-5 py-3"></p></AmsColumn>
      </AmsRow>  
</template>

<script>
export default {

}
</script>

<style lang="scss" scoped>
.skeleton {
      background: transparent;
		animation: shine 1s infinite;
      clip-path: polygon(0 0, 0 0, 0 100%, 0% 100%);
}

@keyframes shine {
   100% {
      background: lightgray;
      clip-path: polygon(0 0, 100% 2%, 100% 100%, 0 100%);

   }
}
</style>