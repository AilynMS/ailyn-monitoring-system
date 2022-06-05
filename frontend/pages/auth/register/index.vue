<template>
    <main class="container mx-auto">
        <AuthHeader />

        <AuthBox
            title="Crea tu cuenta"
            submitText="Crear cuenta"
            :isLoading="isLoading"
            @submit="register">

            <template slot="content">

                <template v-if="hasError">
                    <Notification 
                        type="error"
                        :showIcon="false"
                        classes="text-xs text-center py-3 px-3" 
                        :errorTitle="errorMessage" />
                </template> 

                <ValidationObserver ref="form">
                    <form 
                        action 
                        autocomplete="nope" 
                        v-on:keyup.enter="register">

                        <div class="form-items">
                            <label class="block text font-medium leading-7" for="name">
                                Nombre y apellido
                            </label>

                            <ValidationProvider rules="required" v-slot="{errors}">
                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="name"
                                        v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                        id="name" 
                                        type="text">
                                </div>

                                <small class="text-red-600">{{ errors[0] }}</small>
                            </ValidationProvider>
                        </div>

                        <div class="form-items mt-6">
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
                            <label class="block text font-medium leading-7" for="organization">
                                Organización
                            </label>

                            <ValidationProvider rules="required" v-slot="{errors}">
                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="organization"
                                        v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                                        class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                        id="organization" 
                                        type="text">
                                </div>
                                
                                <small class="text-red-600">{{ errors[0] }}</small>
                            </ValidationProvider> 
                        </div>

                        <div class="form-items mt-6">
                            <label class="block text font-medium leading-7" for="country">
                                País
                            </label>

                            
                            <div class="mt-1 rounded-md">
                                <ValidationProvider rules="required" v-slot="{errors}">
                                    <select
                                        v-model="country"
                                        v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500 shadow-sm'"
                                        class="block w-full bg-white border-gray-300 px-3 py-2 pr-8 rounded leading-tight focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                                        id="country">

                                        <option value disabled>
                                            Selecciona un país
                                        </option>

                                        <option v-for="(country, index) in database.countries" :key="index">
                                            {{ country }}
                                        </option>
                                    </select>

                                    <small class="text-red-600">{{ errors[0] }}</small>
                                </ValidationProvider>
                            </div>            
                        </div>

                        <div class="form-items mt-6">
                            <label class="block text font-medium leading-7" for="password">
                                Contraseña
                            </label>

                            <ValidationProvider 
                                name="password" 
                                rules="required|verify_password|password:@confirm" 
                                v-slot="{errors}">

                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="password"
                                        autocomplete="new-password"
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
                                Confirmar contraseña
                            </label>

                            <ValidationProvider name="confirm" rules="required|password:@password" v-slot="{errors}">
                                <div class="mt-1 rounded-md shadow-sm">
                                    <input 
                                        v-model="confirmPassword"
                                        autocomplete="new-password"
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

                        <div class="form-items mt-6 flex items-center">
                            <input
                                class="mr-2 cursor-pointer form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out"
                                type="checkbox"
                                id="terms"
                                v-model="acceptTerms">

                            <label
                                class="ml-2 block text-sm leading-5 text-gray-900 cursor-pointer" 
                                for="terms">
                                Acepto los términos de uso y la política de privacidad.
                            </label>
                        </div>
                    </form>
                </ValidationObserver>
            </template>
        </AuthBox>

        <AuthFooter 
            contextText="¿Ya tienes una cuenta?"
            url="/auth/login"
            urlText="Inicia sesión" />
    </main>  
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate';

export default {
    middleware: ['guest'],

    components: {
        ValidationObserver,
        ValidationProvider
    },

    head() {
        return {
            title: 'Registro | AilynMS'
        }
    },

    data() {
        return {
            name: '',
            email: '',
            password: '',
            confirmPassword: '',
            organization: '',
            country: '',
            reCaptchaToken: '',
            acceptTerms: false,
            isLoading: false,
            hasError: false,
            errorMessage: '',
            formCompleted: false,
            database: {},
            showCaptcha: true
        }
    },

    mounted() {
        this.getData();
    },

    methods: {
        async register() {
            const { acceptTerms } = this
            this.formCompleted = await this.$refs.form.validate();
            
            if(!this.reCaptchaToken.length) {
                return this.$toast.error('Completa el reCaptcha para continuar')
            }

            if(!acceptTerms) {
                this.$toast.error('Debes aceptar los términos de uso para continuar');
                return false;
            }

            if (this.formCompleted) {
                try {
                    this.showCaptcha = false;
                    this.hasError = false;
                    this.isLoading = true;

                    const savedData = {
                        name: this.name,
                        organization: this.organization,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.confirmPassword,
                        terms: this.acceptTerms,
                        country: this.country,
                        'g-recaptcha-response': this.reCaptchaToken
                    }

                    const response = await this.$axios.$post('/register', savedData);
                    return this.$router.push('/auth/register/verify');

                } catch (e) {
                    console.error(e.response.data);
                    this.isLoading = false;
                    this.showCaptcha = true;
                    this.validateError(e.response.data);
                    this.hasError = true;
                    return this.handleScrollTop();
                }
                
            } else {
                return this.isLoading = false;
            }
        },

        onError(error) {
            this.isLoading = false
            this.$toast.error('Ha ocurrido un error con el reCAPTCHA.');
        },

        onSuccess(token) {
            this.reCaptchaToken = token
        },

        onExpired() {
            this.isLoading = false
            this.$toast.error('El reCAPTCHA ha expirado. Intentalo nuevamente.');
        },

        async getData() {
            try {
                this.isLoading = true;
                const responsePromises = [];
                const countriesReq = this.$axios.$get('/register/countries');
                responsePromises.push(countriesReq);

                const responses = await Promise.all(responsePromises);

                this.$set(this.database, 'countries', responses[0].data); // responses[1] = countriesReq
                return this.isLoading = false;

            } catch (e) {
                console.error(e.response.data);
                return this.isLoading = false;
            }
        },

        validateError(error) {
            const { errors } = error;

            if(errors['g-recaptcha-response']) {
                return this.errorMessage = 'Hubo un error al validar el reCaptcha. Intentalo nuevamente.';
            };

            if(errors['email']) {
                document.getElementById('email').focus();
                return this.errorMessage = 'El correo electrónico ingresado ya ha sido registrado.';
            };

            if(errors['organization']) {
                document.getElementById('organization').focus();
                return this.errorMessage = 'La organización ingresada ya se encuentra registrada.';
            };
        },

        handleScrollTop() {
            return window.scrollTo({ top: 0, behavior: 'smooth' });
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