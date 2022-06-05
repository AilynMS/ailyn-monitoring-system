<template>
    <main class="container mx-auto">
        <AuthHeader />
        
        <AuthBox 
            title="Verifica tu correo electrónico"
            :showSubmitButton="false">

            <template slot="content">
                <p class="text-gray-600">
                    Te hemos enviado un correo con los pasos para restaurar tu contraseña. Comprueba tu bandeja de entrada y sigue las instrucciones.
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