<template>
    <div 
        class="modalCollapsable mt-6">
        <div 
            @click="handleToggler"
            v-bind:class="showItems ? '' : 'border-b'"
            class="tab flex justify-between mt-1 py-2 cursor-pointer focus:border-gray-500 w-full border-gray-300 placeholder-gray-400 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5">
            
            <div class="header">
                <label
                    disabled     
                    class="text font-medium leading-5 cursor-pointer">
                    {{  title ? title : 'Undefined' }}
                </label>
            </div>

            <div class="arrow-tab text-gray-500 text-2xl">
                <fa v-if="!showItems" icon="angle-down" />
                <fa v-else icon="angle-up" />
            </div>
        </div> 

        <div
            v-show="showItems" 
            class="tab-content mt-1 py-6 px-3 border-b border-l border-r rounded-md border-gray-200 shadow-sm bg-gray-100 leading-normal">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ModalCollapsable',

    props: {
        title: String
    },

    data() {
        return {
            showItems: false
        }
    },

    methods: {
        handleToggler() {
            if(this.showItems) return this.showItems = false;
            else return this.showItems = true;
        }
    }
}
</script>