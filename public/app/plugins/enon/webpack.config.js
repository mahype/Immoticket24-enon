const path = require( 'path' );

module.exports = {
	entry: './src/Assets/JS/index.js',
	output: {
		filename: 'bundle.js',
		path: path.resolve(__dirname, './src/Assets/Dist' ),
	},
	mode: 'none'
}
