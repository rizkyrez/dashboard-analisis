/**
 * @type {import('@vue/cli-service').ProjectOptions}
 */
module.exports = {
  transpileDependencies: [
    'vue-echarts',
    'resize-detector'
  ],

  publicPath: process.env.DEPLOY_ENV === 'GH_PAGES'
    ? '/Dashboard Analysis Report/'
    : '/',

  devServer: {
      proxy: 'http://localhost:8000/api/v1/',
  }

}
