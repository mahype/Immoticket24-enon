let mix = require('laravel-mix');

mix
    .ts('src/ts/editor.ts', 'assets/js')
    .sourceMaps();

mix
    .ts('src/ts/frontend.ts', 'assets/js')
    .sourceMaps();

mix
    .sass('src/scss/editor.scss', 'assets/css')
    .sourceMaps()
    .options({
        processCssUrls: false
    });

mix
    .sass('src/scss/frontend.scss', 'assets/css' )
    .sourceMaps()
    .options({
        processCssUrls: false
    });

mix.less('src/_legacy/_legacy.less', 'assets/css');
mix.js('src/_legacy/_legacy.js', 'assets/js');

mix.minify( 'assets/css/editor.css' );
mix.minify( 'assets/css/frontend.css' );

mix.minify( 'assets/js/editor.js' );
mix.minify( 'assets/js/frontend.js' );

mix.minify( 'assets/js/_legacy.js' );
mix.minify( 'assets/css/_legacy.css' );
mix.minify( 'assets/css/_legacy.css' );