<template>
    <header class="sm:mx-auto sm:w-full sm:max-w-md mt-8">
        <img 
            class="mx-auto"
            :src="brandLogo ? brandLogo : '/logo_default.svg'" 
            alt="ailynMS"
            width="150px" 
            height="150px">
    </header>  
</template>

<script>
export default {
    name: 'AuthHeader',

    props: {
        brandLogo: {
            required: false,
            type: String
        }
    }
}
</script>