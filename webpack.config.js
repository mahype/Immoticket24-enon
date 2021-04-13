const path = require('path');
const TerserPlugin = require("terser-webpack-plugin");

module.exports = {
   entry: {
     app: './public/scripts/src/index-reseller.js',
   },
   output: {
     filename: 'reseller.min.js',
     path: path.resolve(__dirname, 'public/scripts/dist'),
     clean: true,
   },
   optimization: {
    minimize: true,
    minimizer: [new TerserPlugin()],
  },
};