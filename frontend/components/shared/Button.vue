<template>
    <button
        @click.prevent="$emit('click')"
        :class=" `${getColor} ${styles} inline-flex justify-center items-center animation ease duration-100 font-bold py-2 px-4 text-sm` ">
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: 'Button',

    props: {
        type: {
            type: String,
            required: false
        },
        styles: {
            type: String,
            required: false
        }
    },

    computed: {
        getColor() {
            if(this.type === 'primary') {
                return 'bg-primary-hoverable text-white primary-focus-shadow'
            }

            if(this.type === 'primary-light') {
                return 'bg-primary-light-hoverable text-white primary-focus-shadow'
            }

            if(this.type === 'error') {
                return 'bg-red-600 text-white primary-focus-shadow'
            }

            if(!this.type) return '';
        }
    }
}
</script>