<template>
    <main class="container mx-auto">
        <AuthHeader />

        <AuthBox
            title="Inicia sesión para continuar"
            submitText="Iniciar sesión"
            :isLoading="isLoading"
            @submit="login">

            <template slot="content">

                <template v-if="hasError">
                    <Notification 
                        type="error"
                        :showIcon="false"
                        classes="text-xs text-center py-3 px-3" 
                        :errorTitle="errorMessage" />
                </template> 

                <template v-if="!hasError & verifySuccess">
                     <Notification 
                        type="success"
                        :showIcon="false"
                        classes="text-xs text-center py-3 px-3" 
                        errorTitle="Su cuenta ha sido verificada correctamente" />                       
                </template> 

                <template v-if="!hasError & verified">
                     <Notification 
                        type="ok"
                        :showIcon="false"
                        classes="text-xs text-center py-3 px-3" 
                        errorTitle="Su cuenta ya está verificada" />                       
                </template> 

                <ValidationObserver ref="form">
                    <form action v-on:keyup.enter="login">
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
                                <div class="flex items-center justify-between">
                                    <label 
                                        for="password" 
                                        class="block text font-medium leading-7">
                                        Contraseña
                                    </label>

                                    <div class="text-sm">
                                        <nuxt-link 
                                            tabindex="-1" 
                                            to="/auth/recovery_password" 
                                            class="font-medium text-primary-hoverable focus:outline-none focus:underline transition ease-in-out duration-150">
                                            ¿Olvidaste tu contraseña?
                                        </nuxt-link>
                                    </div>
                                </div>

                            <ValidationProvider rules="required" v-slot="{errors}">
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
            contextText="¿No tienes una cuenta?"
            url="/auth/register" 
            urlText="Registrate" />
    </main>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";

export default {
    middleware: ['guest'],

    components: {
        ValidationObserver,
        ValidationProvider
    },

    head() {
        return {
            title: 'Iniciar sesión | AilynMS'
        }
    },

    data() {
        return {
            email: '',
            password: '',
            reCaptchaToken: '',
            showCaptcha: process.env.ENABLE_RECAPTCHA == 'false' ? false : true,
            isLoading: false,
            hasError: false,
            errorMessage: '',
            formCompleted: false,
            verifySuccess: false,
            verified: false
        }
    },

    mounted() {
        console.log('1: ' + process.env)
        console.log('2: ' + process.env.ENABLE_RECAPTCHA == 'false' ? false : true)
        this.verificationExist();
        this.verificationSuccessfully();
    },
    
    methods: {
        async login() {
            this.formCompleted = await this.$refs.form.validate();

            if(this.showCaptcha == true && !this.reCaptchaToken.length) {
                return this.$toast.error('Completa el reCaptcha para continuar')
            }

            if (this.formCompleted) {
                try {
                    const data = {
                        email: this.email,
                        password: this.password,
                        validatecaptcha: this.showCaptcha,
                        'g-recaptcha-response': this.reCaptchaToken
                    };

                    const response = await this.$auth.loginWith('local', { data });
                    
                    window.location = `/${this.$auth.user.tenant.slug}/verificaciones/web`;

                    this.showCaptcha = false;
                    this.verifySuccess = false;
                    this.hasError = false;
                    this.isLoading = true;
                    return this.showCaptcha = true;
                    
                } catch (e) {
                    this.isLoading = false;
                    this.validateError(e.response.data);
                    this.showCaptcha = process.env.ENABLE_RECAPTCHA == 'false' ? false : true;
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

            if(error.message == 'Cuenta no verificada') {
                return this.$router.push('/auth/register/verify?success=false');
            };

            if(errors['g-recaptcha-response']) {
                return this.errorMessage = 'Hubo un error al validar el reCaptcha. Intentalo nuevamente';
            };

            if(errors) {
                return this.errorMessage = 'Los datos ingresados no concuerdan con nuestros registros'
            };
        },

        verificationExist() {
            if(this.$route.query.verified) this.verified = true;
        },

        verificationSuccessfully() {
            if(this.$route.query.success) this.verifySuccess = true;
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