let mix = require('laravel-mix');

mix
    .js('public/scripts/src/reseller.js', 'public/scripts/dist')
    .setPublicPath('public/scripts/dist')
    .sourceMaps();


mix.minify( 'public/scripts/dist/reseller.js' );