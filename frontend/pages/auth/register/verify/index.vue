<template>
    <main class="container mx-auto">
        <AuthHeader />
        
        <AuthBox 
            title="Verifica tu correo electrónico"
            :showSubmitButton="false">

            <template slot="content">
                <p class="text-gray-600">
                    Te hemos enviado un correo para asegurarnos de que eres el propietario. Comprueba tu bandeja de entrada y sigue las instrucciones para activar tu cuenta.
                </p>
            </template>
        </AuthBox>
    </main>    
</template>

<script>
import AuthHeader from '~/components/auth/AuthHeader';
import AuthBox from '~/components/auth/AuthBox';

export default {
    components: {
        AuthHeader,
        AuthBox
    },

    head() {
        return {
            title: 'Verificación de correo electrónico | AilynMS'
        }
    },
}
</script>

<style>

</style>