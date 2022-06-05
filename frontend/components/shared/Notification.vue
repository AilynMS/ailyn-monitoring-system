<template>
    <div
        :class="`${type} ${classes} rounded-lg mb-8 justify-center items-center flex flex-col my-auto`"
        role="alert">

        <template v-if="type">
            <fa v-if="type === 'error' & showIcon" class="text-5xl mb-3" icon="exclamation-triangle" />
            <fa v-if="type === 'success' & showIcon" class="text-5xl mb-3" icon="check" />
            <fa v-if="type === 'warning' & showIcon" class="text-5xl mb-3" icon="exclamation" />
            <fa v-if="type === 'ok' & showIcon" class="text-5xl mb-3" icon="thumbs-up" />
        </template>

        <h3 class="font-bold">{{ errorTitle ? errorTitle : '' }}</h3>

        <span class="block font-medium text-sm sm:inline">
            <small>{{ errorText ? errorText : '' }}</small>
        </span>

        <span 
            v-if="showCloseButton"
            @click="$emit('close', true)" 
            class="absolute top-0 bottom-0 right-0 px-4 py-3">

            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>
</template>

<script>
export default {
    name: 'Notification',

    props: {
        errorTitle: {
            required: false,
            type: String
        },
        errorText: {
            required: false,
            type: String
        },
        classes: {
            required: false,
            type: String
        },
        showCloseButton: {
            required: false,
            default: false,
            type: Boolean
        },
        type: {
            required: false,
            type: String
        },
        showIcon: {
            required: false,
            type: Boolean,
            default: false
        }
    }
}
</script>

<style lang="scss" scoped>
    .error {
        @apply bg-red-200 text-red-600;
    }

    .success {
        @apply bg-green-200 text-green-600;
    }

    .ok {
        @apply bg-blue-200 text-blue-600;
    }

    .warning {
        @apply bg-yellow-200 text-yellow-600;
    }
</style>