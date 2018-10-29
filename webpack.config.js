const CleanWebpackPlugin = require("clean-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");

const path = require("path");

module.exports = {
  entry: {
    cms: "./gsd-development/js/entry",
    frontend: "./gsd-frontend/development/js/entry"
  },
  mode: "development",
  optimization: {
    minimizer: [new OptimizeCSSAssetsPlugin({}), new UglifyJsPlugin({})]
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader"
        }
      },
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"]
      }
    ]
  },
  plugins: [
    new CleanWebpackPlugin([path.resolve(__dirname, "./gsd-resources/assets")]),
    new MiniCssExtractPlugin({
      filename: "assets/[name].css"
    })
  ],
  output: {
    filename: "assets/[name].js",
    path: path.resolve(__dirname, "./gsd-resources")
  }
};