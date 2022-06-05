<template>
    <td :data-tooltip="title" class="px-3 py-3 border-b whitespace-no-wrap border-gray-300 border-b text-sm leading-5 font-medium text-gray-800">
        <slot></slot>
    </td>  
</template>

<script>
export default {
    name: 'AmsColumn',

    props: {
        title: {
            required: false,
            type: String
        }
    }
}
</script>