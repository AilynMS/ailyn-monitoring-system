<template>
    <header class="flex bg-white text-gray-700 py-3">

        <div class="container mx-auto max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex lg:justify-between xl:justify-start md:justify-start justify-between">
            <!-- brand -->
            <div class="brandlogo flex items-center flex-shrink-0 text-white mr-6">
                <img 
                    class="logo" 
                    src="/brand_responsive.png" 
                    alt="AilynMS Logo"/>
            </div>

            <!-- Hamburguer icon -->
            <div 
                @click="navbarToggler"
                class="cursor-pointer rounded burger flex-col justify-center items-center xl:hidden lg:hidden md:hidden flex py-2">
                <span class="bar"></span>
                <span class="bar my-1"></span>
                <span class="bar"></span>
            </div>

            <nav
                v-bind:class="showNavbar ? 'active' : ''"
                class="xl:flex lg:flex md:flex responsive bg-white my-auto w-full" 
                ref="menu">

                <ul class="w-full flex lx:flex-row lg:flex-row md:flex-row flex-col xl:px-0 lg:px-0 md:px-0 px-6">
                   
                    <!-- Dropdown item -->
                    <li 
                        @click="showNavbar = false" 
                        class="my-auto xl:mr-1 lg:mr-1 md:mr-1 mr-0 dropdown-toggler">

                        <nuxt-link
                            class="navbar-item item-2 py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700"
                            to="#">
                            <fa icon="list" /> Verificaciones
                        </nuxt-link>

                        <div class="dropdown origin-top-right absolute left py-3 px-3 w-dropdown rounded-md shadow-lg bg-white">
                            <nuxt-link
                                class="flex md:justify-between md:flex-row-reverse w-full navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                                :to=" `/${slug && slug}/verificaciones/web` ">
                                <fa class="my-auto md:mr-0 mr-2" icon="wifi" /> Verificaciones WEB
                            </nuxt-link>

                            <hr class="my-auto border-gray-200 mx-1 md:block hidden">

                            <nuxt-link
                                class="flex md:justify-between md:flex-row-reverse w-full navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                                :to=" `/${slug && slug}/verificaciones/dns` ">
                                <fa class="my-auto md:mr-0 mr-2" icon="network-wired" /> Verificaciones DNS
                            </nuxt-link>

                            <hr class="my-auto border-gray-200 mx-1 md:block hidden">

                            <nuxt-link
                                class="flex md:justify-between md:flex-row-reverse w-full navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                                :to=" `/${slug && slug}/verificaciones/ping` ">
                                 <fa class="my-auto md:mr-0 mr-2" icon="server" /> Verificaciones PING
                            </nuxt-link>

                            <hr class="my-auto border-gray-200 mx-1 md:block hidden">

                            <nuxt-link
                                class="flex md:justify-between md:flex-row-reverse w-full navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                                :to=" `/${slug && slug}/verificaciones/tcp` ">
                                 <fa class="my-auto md:mr-0 mr-2" icon="desktop" /> Verificaciones TCP/UDP
                            </nuxt-link>

                            <hr class="my-auto border-gray-200 mx-1 md:block hidden">

                            <nuxt-link
                                class="flex md:justify-between md:flex-row-reverse w-full navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                                :to=" `/${slug && slug}/verificaciones/snmp` ">
                                <fa class="my-auto md:mr-0 mr-2" icon="project-diagram" /> Verificaciones SNMP
                            </nuxt-link>
                        </div>
                    </li>

                    <!-- Link item -->
                    <li 
                        @click="showNavbar = false" 
                        class="my-auto xl:mr-1 lg:mr-1 md:mr-1 mr-0">

                        <nuxt-link
                            class="navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                            :to=" `/${slug && slug}/configuracion` ">
                            <fa class="my-auto md:mr-0 mr-2" icon="cog" /> Configuración
                        </nuxt-link>

                    </li>

                    <!-- Link item MD/SM-->
                    <li class="xl:mr-1 lg:mr-1 md:mr-1 mr-0 block md:hidden ">
                        <nuxt-link
                            class="cursor-pointer navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                            :to=" `/${slug && slug}/perfil` ">
                            <fa class="my-auto md:mr-0 mr-2" icon="user" /> Perfil
                        </nuxt-link>

                        <a
                            class="cursor-pointer navbar-item py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                            @click.prevent="logout">
                            <fa class="my-auto md:mr-0 mr-2" icon="door-open" /> Cerrar sesión
                        </a>
                    </li>

                    <!-- Link item XL/LG-->
                    <li class="xl:ml-auto lg:ml-auto md:ml-auto xl:block lg:block md:block hidden">
                        <NavigationMenu>
                            <div class="py-1 flex flex-col" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a
                                    class="py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700" 
                                    >
                                    {{ $auth.user ? $auth.user.name : 'Usuario' }}
                                    <p class="text-gray-500">{{ $auth.user ? $auth.user.tenant.name : 'Empresa'}}</p>
                                </a>

                                <div class="separator mb-2"> <hr> </div>
                                
                                <nuxt-link
                                    class="cursor-pointer py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700 hover:text-gray-900" 
                                    :to=" `/${slug && slug}/perfil` ">
                                    Perfil
                                </nuxt-link>

                                <a
                                    class="cursor-pointer py-2 inline-block text-sm font-medium rounded py-1 px-3 transp-hoverable text-gray-700 hover:text-gray-900" 
                                    @click.prevent="logout">
                                    Cerrar sesión
                                </a>
                            </div>
                        </NavigationMenu>
                    </li>

                </ul>
            </nav>
        </div>

    </header>
</template>

<script>
import NavigationMenu from '~/components/shared/navigation/NavigationMenu.vue';

export default {
    name: 'Navigation',

    components: {
        NavigationMenu
    },

    data() {
        return {
            showNavbar: false
        }
    },

    computed: {
        slug() {
            if(this.$auth.user) return this.$auth.user.tenant.slug;
        }
    },

    methods: {
        async logout() {
            await this.$auth.logout();
        },

        navbarToggler() {
            return this.showNavbar = !this.showNavbar;
        }
    }
}
</script>

<style lang="scss" scoped>
    header {
        .brandlogo {
            max-width: 45px;
            width: 100%;

            img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }

        @media(max-width: 768px) {
            .responsive {
                position: absolute;
                top: 3.6rem;
                left: -100%;
                height: calc(100vh - 4rem);
                width: 100%;
                padding-top: 0.5rem;
                z-index: 100;

                &.active {
                    left: 0;
                }

                .navbar-item {
                    display: inline-block;
                    width: 100%;
                    margin-top: 0.3rem;
                }
            }
        }

        .navbar-item:hover {
            background: rgba(146, 146, 146, 0.137);
            color: #495567;
            transition: .1s linear;
        }

        .burger {
            max-width: 40px;
            width: 100%;
            height: 100%;

            &:hover {
                background: rgba(255, 106, 0, 0.137);

                .bar {
                    background: #FF6C00;
                }
            }

            .bar {
                width: 30px;
                height: 3px;
                border-radius: 50px;
                @apply bg-gray-700;
            }
        }
        
        .dropdown-toggler {
            @media (max-width: 768px) {
                .navbar-item.item-2 {
                    display: none;
                }
            }

            .dropdown {
                display: none;
                z-index: 11;

                @media (max-width: 768px) {
                    & {
                        display: block;
                        position: static !important;
                        box-shadow: none;
                        padding: 0;
                        width: 100%;
                    }
                }
            }

            &:hover > .dropdown {
                display: block;
            }
        }
    }

    @media(min-width: 768px) {
        .w-dropdown {
            width: 260px;
        }
    }
</style>
