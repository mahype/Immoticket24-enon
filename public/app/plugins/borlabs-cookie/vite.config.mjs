/*
 *  Copyright (c) 2024 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import {viteStaticCopy} from 'vite-plugin-static-copy';
import { visualizer } from 'rollup-plugin-visualizer';

// https://vitejs.dev/config/
export default defineConfig({
  root: 'resources',
  // https://vitejs.dev/guide/build.html#advanced-base-options
  experimental: {
    renderBuiltUrl(filename, options) {
      if (options.hostType === 'js') {
        return { runtime: '(() => window.borlabsCookieConfig.settings.pluginUrl + \'/assets/\' + ' + JSON.stringify(filename) + ')()' };
      } else {
        return { relative: true };
      }
    }
  },
  server: {
    host: true,
    watch: {
      usePolling: true,
    },
    hmr: {
      protocol: 'ws',
      host: 'wordpress-vite.borlabs.test',
      port: 80,
    },
  },
  build: {
    manifest: 'manifest.json',
    minify: 'esbuild',
    target: 'es2015',
    emptyOutDir: true,
    rollupOptions: {
      // make sure to externalize deps that shouldn't be bundled
      // into your library
      input: [
        'resources/typescript/frontend/borlabs-cookie.ts',
        'resources/typescript/frontend/borlabs-cookie-iabtcf.ts',
        'resources/typescript/frontend/borlabs-cookie-prioritize.ts',
        'resources/scss/admin/wordpress-admin.scss',
        'resources/typescript/admin/borlabs-cookie-admin.ts',
        'resources/scss/frontend/borlabs-cookie.scss',
      ],
      output: {
        dir: 'assets/',
        entryFileNames: 'javascript/[name].min.js',
        manualChunks: {
          'vue': ['vue'],
        },
        // chunkFileNames: 'javascript/[name].[hash].js',
        chunkFileNames: (assetInfo) => {
          // fixes weird chunk names for some vue components
          // there is an open issue in the vite-plugin-vue https://github.com/vitejs/vite-plugin-vue/issues/19
          if (assetInfo.name && assetInfo.name.endsWith('.vue_vue_type_script_setup_true_lang')) {
            return `javascript/${assetInfo.name.slice(0, -36)}.[hash:8].min.js`;
          } else {
            return 'javascript/[name].[hash:8].min.js';
          }
        },
        assetFileNames: '[ext]/[name].[hash].min.[ext]',
      },
    },
  },
  plugins: [
    vue(),
    visualizer(),
    viteStaticCopy({
      targets: [
        {
          src: '../node_modules/@iabtechlabtcf/stub/lib/stub.js',
          dest: '../../assets/javascript/',
          rename: 'borlabs-cookie-tcf-stub.min.js',
        }
      ]
    })
  ],
});
