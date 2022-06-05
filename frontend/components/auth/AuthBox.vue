<template>
    <section class="box-wrapper mt-8 bg-white py-10 px-4 shadow-lg rounded-lg sm:px-10">
        <div class="header mb-6">
            <template v-if="title">
                <h1 class="title">
                    {{ title ? title : 'Undefined title' }}
                </h1>
            </template>

            <template v-if="description">
                <p class="">{{ description ? description : ''}}</p>
            </template>
        </div>

        <slot name="content"></slot>

        <div class="mt-8" v-if="showSubmitButton">
            <span 
                v-bind:class="!isLoading ? 'shadow-sm' : null"
                class="block w-full rounded-md">
                <button 
                    v-if="!isLoading"
                    @click.prevent="$emit('submit', true)"
                    type="submit" 
                    class="submitButton inline-flex outline-none primary-focus-shadow justify-center items-center w-full bg-primary-hoverable text-white font-bold py-2 px-4 rounded">
                    {{ submitText }}
                </button>

                <span
                    v-else
                    @click.prevent=""
                    class="loadingButton inline-flex justify-center items-center w-full w-100 outline-none text-white font-bold py-2 px-4 rounded">
                    <LoadingEffect />
                </span>
            </span>
        </div>
    </section>
</template>

<script>
import LoadingEffect from '~/components/shared/LoadingEffect';

export default {
    name: 'AuthBox',

    components: {
        LoadingEffect
    },

    props: {
        title: {
            required: false,
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
        isLoading: {
            required: false,
            default: false,
            type: Boolean
        },
        showSubmitButton: {
            required: false,
            default: true,
            type: Boolean
        }
    }
}
</script>

<style lang="scss" scoped>
    .box-wrapper {
        max-width: 30rem;
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }
</style>