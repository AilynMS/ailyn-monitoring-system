<template>
    <tr v-bind:class="hoverable && 'hover:bg-gray-100'">
        <slot></slot>
    </tr>
</template>

<script>
export default {
    name: 'AmsRow',

    props: {
        hoverable: {
            required: false,
            type: Boolean,
            default: false
        }
    }
}
</script>