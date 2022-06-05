<template>
<div class="modal-component fixed z-10 inset-0 overflow-y-auto" @keyup.esc="onCancel()">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div @click="onCancel" class="fixed inset-0 transition-opacity bg-blur">
            <div class="absolute inset-0 bg-gray-700 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-8 pt-8 pb-5 sm:p-5 sm:pb-5">
                <div class="flex flex-col">
                    <div class="mb-6 sm:mt-0 sm:ml-4">
                        <h3 class="title" id="modal-headline">
                            {{ title ? title : 'Undefined title'}}
                        </h3>

                        <div v-if="description">
                            <p class="text-sm leading-5 text-gray-600"> {{ description }} </p>
                        </div>
                    </div>

                    <div class="modal-body text-left sm:mt-0 sm:ml-4 pr-5">
                        <slot></slot>
                    </div>
                </div>
            </div>

            <template v-if="showButtons">
                <div v-if="!isLoading" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                    <span class="flex w-full sm:ml-3 sm:w-auto">
                        <Button @click="onSubmit" type="primary" styles="outline-none rounded-md w-full">

                            {{ submitText ? submitText : 'Guardar' }}
                        </Button>
                    </span>

                    <span class="mt-3 flex w-full sm:mt-0 sm:w-auto">
                        <Button @click="onCancel" styles="outline-none text rounded-md w-full">
                            Cancelar
                        </Button>
                    </span>
                </div>

                <div v-else @click.prevent="" class="loadingButton mb-5 inline-flex justify-center items-center w-full w-100 outline-none text-white font-bold py-2 px-4 rounded">
                    <LoadingEffect />
                </div>
            </template>
        </div>

    </div>
</div>
</template>

<script>
import LoadingEffect from '~/components/shared/LoadingEffect';
export { default as ModalCollapsable } from '~/components/shared/modal/ModalCollapsable.vue';

export default {
    name: 'Modal',

    components: {
        LoadingEffect
    },

    props: {
        isLoading: {
            required: false,
            type: Boolean,
            default: false
        },
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
            type: String
        },
        showButtons: {
            required: false,
            type: Boolean,
            default: true
        }
    },

    methods: {
        onCancel() {
            return this.$emit('onCancel', true);
        },

        onSubmit() {
            return this.$emit('onSubmit', true);
        }
    }
}
</script>

<style lang="scss" scoped>
.modal-component {
    .modal-body {
        max-height: 350px;
        height: 100%;
        overflow-y: scroll;

        &::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        &::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        &::-webkit-scrollbar-thumb:hover {
            background: #b3b3b3;
        }

        &::-webkit-scrollbar-thumb:active {
            background-color: #999999;
        }
    }
}
</style>