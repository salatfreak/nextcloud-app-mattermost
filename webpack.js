const path = require('path')
const webpack = require('webpack')

const StyleLintPlugin = require('stylelint-webpack-plugin')

const appVersion = process.env.npm_package_version
const buildMode = process.env.NODE_ENV
const isDev = buildMode === 'development'
console.info('Building', process.env.npm_package_name, appVersion, '\n')

module.exports = {
  mode: buildMode,
  devtool: isDev ? '#cheap-source-map' : '#source-map',
  entry: {
    main: './src/main.js',
    'settings-admin': './src/settings-admin.js', 
  },
  output: {
    path: path.resolve('./js'),
    publicPath: '/js/',
    filename: `[name].js?v=[contenthash]`,
    chunkFilename: `[name].js?v=[contenthash]`,
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: ['vue-style-loader', 'css-loader'],
      },
      {
        test: /\.js$/,
        use: 'eslint-loader',
        exclude: /node_modules/,
        enforce: 'pre',
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
      },
    ],
  },
  resolve: {
    extensions: ['*', '.js'],
    symlinks: false,
  },
}
