<template>
    <main class="container mx-auto">
        <AuthHeader />
        
        <AuthBox
            title="Restablecer contraseña"
            submitText="Continuar"
            :isLoading="isLoading"
            @submit="recovery">

            <template slot="content">
                <template v-if="hasError">
                    <Notification 
                        type="error"
                        :showIcon="false"
                        classes="text-xs text-center py-3 px-3" 
                        :errorTitle="errorMessage" />
                </template>

                <ValidationObserver ref="form">
                    <form action>
                        <div class="form-items">
                            <label class="block text font-medium leading-7" for="email">
                                Correo electrónico
                            </label>

                            <ValidationProvider rules="required|email" v-slot="{errors}">
                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="email"
                                        v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                        id="email" 
                                        type="text">
                                </div>

                                <small class="text-red-600">{{ errors[0] }}</small>
                            </ValidationProvider>  
                        </div>

                        <div class="form-items mt-6">
                            <label class="block text font-medium leading-7" for="password">
                                Nueva contraseña
                            </label>

                            <ValidationProvider 
                                name="password" 
                                rules="required|verify_password|password:@confirm" 
                                v-slot="{errors}">

                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="password"
                                        v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                        id="password" 
                                        type="password">
                                </div>
                                
                                <small class="text-red-600">{{ errors[0] }}</small>
                            </ValidationProvider> 
                        </div>

                        <div class="form-items mt-6">
                            <label class="block text font-medium leading-7" for="confirmPassword">
                                Confirmar nueva contraseña
                            </label>

                            <ValidationProvider name="confirm" rules="required|password:@password" v-slot="{errors}">
                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="confirmPassword"
                                        v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                        id="confirmPassword" 
                                        type="password">
                                </div>

                                <small class="text-red-600">{{ errors[0] }}</small>
                            </ValidationProvider>  
                        </div>

                        <div class="form-items mt-6" v-if="showCaptcha">
                            <recaptcha 
                                @error="onError" 
                                @success="onSuccess" 
                                @expired="onExpired" />
                        </div>
                    </form>
                </ValidationObserver>
            </template>
        </AuthBox>

        <AuthFooter 
            contextText="¿Recuerdas tu contraseña?"
            url="/auth/login"
            urlText="Inicia sesión" />
    </main>    
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";

export default {
    components: {
        ValidationObserver,
        ValidationProvider
    },

    head() {
        return {
            title: 'Restablecer contraseña | AilynMS'
        }
    },

    data() {
        return {
            email: '',
            password: '',
            confirmPassword: '',
            reCaptchaToken: '',
            token: '',
            isLoading: false,
            hasError: false,
            errorMessage: '',
            formCompleted: false,
            showCaptcha: true
        }
    },

    mounted() {
        if(this.$route.params.token) this.token = this.$route.params.token;
    },

    methods: {
        async recovery() {
            this.formCompleted = await this.$refs.form.validate();

            if(!this.reCaptchaToken.length) {
                return this.$toast.error('Completa el reCaptcha para continuar')
            }

            if (this.formCompleted) {
                try {
                    this.showCaptcha = false;
                    this.hasError = false;
                    this.isLoading = true;

                    const data = {
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.confirmPassword,
                        token: this.token,
                        'g-recaptcha-response': this.reCaptchaToken
                    };

                    const response = await this.$axios.$post('/password/reset', data);
                    return this.$router.push('/auth/login?success=true');

                } catch (e) {
                    this.isLoading = false;
                    this.validateError(e.response.data);
                    this.showCaptcha = true;
                    return this.hasError = true;
                }
                
            } else {
                return this.isLoading = false;
            }
        },

        onError(error) {
            this.isLoading = false;
            this.$toast.error('Ha ocurrido un error con el reCAPTCHA.');
        },

        onSuccess(token) {
            this.reCaptchaToken = token;
        },

        onExpired() {
            this.isLoading = false;
            this.$toast.error('El reCAPTCHA ha expirado. Intentalo nuevamente.');
        },

        validateError(error) {
            const { errors } = error;

            if(errors['g-recaptcha-response']) {
                return this.errorMessage = 'Hubo un error al validar el reCaptcha. Intentalo nuevamente.';
            };

            if(errors) {
                return this.errorMessage = 'El email ingresado no concuerdan con nuestros registros.'
            };
        }
    }
}
</script>

<style lang="scss" scoped>
.g-recaptcha {
    margin: 0 auto !important;
    width: auto !important;
    height: auto !important;
    text-align: -webkit-center;
    text-align: -moz-center;
    text-align: -o-center;
    text-align: -ms-center;
}
</style>