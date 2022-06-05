<template>
    <footer class="flex justify-center mt-6 mb-10">
        <p class="mr-2 text-gray-600">
            {{ contextText ? contextText : 'Texto indefinido' }}
        </p>

        <nuxt-link 
            class="text-primary-hoverable" 
            :to="url ? url : '/'">
            {{ urlText ? urlText : 'Ingresar' }}
        </nuxt-link>
    </footer>
</template>

<script>
export default {
    name: 'AuthFooter',
    props: {
        url: {
            required: false,
            type: String
        },
        urlText: {
            required: false,
            type: String
        },
        contextText: {
            required: false,
            type: String
        }
    }
}
</script>