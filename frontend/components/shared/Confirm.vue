<template>
    <div class="fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 transition-opacity duration-500 bg-blur">
            <div class="absolute inset-0 bg-gray-700 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">

                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <!-- Heroicon name: exclamation -->
                        <fa class="text-lg" icon="exclamation" :class="getColor" />
                    </div>

                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                            {{ title ? title : 'Sin titulo' }}
                        </h3>

                        <div class="mt-2" v-if="description">
                            <p class="text-sm leading-5 text-gray-500">
                                {{ description }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div v-if="!isLoading" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                    <Button @click="onSubmit" :type="type" styles="outline-none rounded-md w-full">
                        {{ submitText }}
                    </Button>
                </span>

                <span class="mt-3 flex w-full rounded-md sm:mt-0 sm:w-auto">
                    <Button @click="onCancel" styles="outline-none text rounded-md w-full">
                        Cancelar
                    </Button>
                </span>
            </div>

            <div v-else @click.prevent="" class="loadingButton mb-5 inline-flex justify-center items-center w-full w-100 outline-none text-white font-bold py-2 px-4 rounded">
                <LoadingEffect />
            </div>
        </div>
    </div>
    </div>    
</template>

<script>
export default {
    name: 'confirm',

    props: {
        title: {
            required: true,
            type: String
        },
        description: {
            required: false,
            type: String
        },
        submitText: {
            required: false,
            type: String,
            default: 'Guardar'
        },
        type: {
            required: false,
            type: String,
            default: 'primary'
        },
        isLoading: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    
    computed: {
        getColor() {
            if(this.type === 'primary') return 'text-primary';
            if(this.type === 'error') return 'text-red-600';
            if(this.type === 'success') return 'text-green-600';
            if(this.type === 'ok') return 'text-blue-600';
            if(this.type === 'warning') return 'text-yellow-600';
            return;          
        }
    },

    methods: {
        onSubmit() {
            this.$emit('onSubmit', true);
        },

        onCancel() {
            this.$emit('onCancel', true);
        }
    }
}
</script>