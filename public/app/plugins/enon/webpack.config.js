const path = require( 'path' );

module.exports = {
	entry: [
		'./src/Assets/JS/index.js',
		'./src/Assets/Scss/index.scss'
	],
	output: {
		filename: 'bundle.js',
		path: path.resolve(__dirname, './src/Assets/Dist' ),
	},
	mode: 'none',
	module: {
		rules: [
			{
				test: /\.s[c|a]ss$/,
				use: [
					{
						loader: 'style-loader',
						options: {
							injectType: 'styleTag'
						}
					},
					'css-loader',
					'postcss-loader',
					'sass-loader?sourceMap'
				]
			}
		]
	}
}
