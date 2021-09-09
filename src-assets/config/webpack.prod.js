const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');

const TerserPlugin = require("terser-webpack-plugin");

module.exports = merge(common, {
	mode: 'production',
	devtool: false,
	plugins: [
		new TerserPlugin({
			terserOptions: {
				compress: {
					drop_console: true
				}
			}
		})
	],
	optimization: {
		minimize: true
	},
	performance: {
		hints: false,
		maxEntrypointSize: 512000,
		maxAssetSize: 512000,
	},
});