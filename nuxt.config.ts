// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  modules: ['@ant-design-vue/nuxt'],
  app: {
    head: {
      title: 'Cửa hàng mỹ phẩm', // Tiêu đề chung cho toàn bộ trang web
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Mô tả chung của website' }
      ]
    },
    layoutTransition: true // optional
  },

})