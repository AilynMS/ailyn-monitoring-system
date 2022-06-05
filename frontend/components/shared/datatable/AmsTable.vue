<template>
    <section class="datatable-component border min-w-full border-gray-300 rounded-md overflow-y-hidden overflow-x-auto shadow-sm bg-white">
        <table class="overflow-x-scroll w-full">
            <thead class="border-b border-gray-200">
                <tr class="">
                    <th 
                        class="px-3 py-3 bg-gray-100 text-left text-xs leading-5 font-medium text-gray-700 uppercase whitespace-no-wrap"
                        v-for="(data, index) in header" :key="index">
                        {{ data }}
                    </th>
                </tr>
            </thead>

            <tbody>
                <slot></slot>
            </tbody>

        </table>
    </section>
</template>

<script>
// Proxy export all related components.
export { default as AmsColumn } from '~/components/shared/datatable/AmsColumn.vue';
export { default as AmsRow } from '~/components/shared/datatable/AmsRow.vue';

export default {
    name: 'AmsTable',

    props: {
        header: {
            required: false,
            type: Array
        },
        isLoading: {
            required: false,
            type: Boolean,
            default: false
        }
    }
}
</script>
