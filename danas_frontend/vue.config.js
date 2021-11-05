/**
 * @type {import('@vue/cli-service').ProjectOptions}
 */
module.exports = {
  transpileDependencies: [
    'vue-echarts',
    'resize-detector'
  ],

  publicPath: process.env.DEPLOY_ENV === 'GH_PAGES'
    ? '/admin-one-vue-tailwind/'
    : '/',

  devServer: {
      proxy: 'http://localhost:8000/api/v1/',
  }

}
