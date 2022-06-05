export default {
  // Disable server-side rendering (https://go.nuxtjs.dev/ssr-mode)
  ssr: false,

  // Target (https://go.nuxtjs.dev/config-target)
  target: 'static',

  loading: false,

  // Global page headers (https://go.nuxtjs.dev/config-head)
  head: {
    title: 'AilynMS',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'AilynMS' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS (https://go.nuxtjs.dev/config-css)
  css: [
    '~assets/scss/app.scss'
  ],
  
  // Plugins to run before rendering page (https://go.nuxtjs.dev/config-plugins)
  plugins: [
    '~/plugins/vee-validate',
    '~/plugins/vue-select',
    '~/plugins/short',
    '~/plugins/vue2-datepicker'
  ],

  // Auto import components (https://go.nuxtjs.dev/config-components)
  components: true,

  // Modules for dev and build (recommended) (https://go.nuxtjs.dev/config-modules)
  buildModules: [
    // https://go.nuxtjs.dev/tailwindcss
    '@nuxtjs/tailwindcss',
    ['@nuxtjs/moment', {
      defaultLocale: 'es',
      locales: ['es']
    }],
  ],

  // Modules (https://go.nuxtjs.dev/config-modules)
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/toast',
    '@nuxtjs/recaptcha',
    '@nuxtjs/auth',
    [
      'nuxt-fontawesome', {
        component: 'fa',
        imports: [
          {
            set: '@fortawesome/free-solid-svg-icons',
            icons: ['fas']
          },

        ]
      }
    ]
  ],

  moment: {
    defaultTimezone: 'America/Bogota'
  },

  // Build Configuration (https://go.nuxtjs.dev/config-build)
  build: {
  },

  router: {
    linkActiveClass: 'bg-primary-selected'
  },

  axios: {
    baseURL: process.env.API_URL
  },

  router: {
    extendRoutes(routes, resolve) {
      routes.push(
      {
        name: 'setNewPassword',
        path: '/reset_password/:token',
        component: resolve(__dirname, 'pages/auth/reset_password')
      });
    }
  },

  toast: {
    position: 'top-center',
    theme: 'bubble',
    duration: 3500
  },

  recaptcha: {
    hideBadge: false,
    language: 'es',
    siteKey: process.env.RECAPTCHA_KEY,
    version: 2,
    size: 'normal'
  },

  auth: {
    strategies: {
      local: {
        endpoints: {
          login: {
            url: '/login',
            method: 'post',
            propertyName: 'data.token'
          },
          logout: {
            url: '/logout',
            method: 'post'
          },
          user: {
            url: '/current-user',
            method: 'get',
            propertyName: 'data.user'
          }
        }
      }
    },
    redirect: {
      login: '/auth/login',
      home: '/'
    }
  }
}
