const path = require( 'path' );

module.exports = {
    entry: './assets/src/upload.js',
    output: {
        path: path.resolve( __dirname, 'assets' ),
        filename: 'upload.js'
    },
    module: {
        rules: [{
            test: /\.js$/,
            exclude: /node_modules/,
            use: {
                loader: 'babel-loader',
                options: {
                    presets: ['@babel/preset-env']
                }
            }
        }]
    }
}
