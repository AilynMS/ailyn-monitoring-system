<template>
    <div class="navigationMenu-component relative inline-block text-left" @click.prevent="handleDropdown">
        <div>
            <span class="rounded-md shadow-sm">
                <button 
                    type="button" 
                    class="inline-flex justify-centers w-full rounded-md bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-800 focus:outline-none transition ease-in-out duration-150" id="options-menu" 
                    aria-haspopup="true" 
                    title="Menú"
                    aria-expanded="true">
                    
                    <div class="profile-circle text-lg font-bold">
                        {{ username }} 
                    </div>
                </button>
            </span>
        </div>
        
        <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg" v-if="showDropdown">
            <div class="rounded-md bg-white shadow-xs">
                <slot></slot>
            </div>
        </div>
    </div>  
</template>

<script>
export default {
    name: 'NavigationMenu',

    data() {
        return {
            showDropdown: false
        }
    },

    computed: {
        username() {
            if(this.$auth.user) return this.$auth.user.name[0];
        }
    },

    methods: {
        handleDropdown() {
            return this.showDropdown = !this.showDropdown;
        }
    }
}
</script>

<style lang="scss" scoped>
    .navigationMenu-component {
        z-index: 10;
        
        .profile-circle {
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 50%;
            background: rgb(230, 230, 230);
        }
    }
</style>