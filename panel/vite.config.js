import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import path from 'path'
import fs from 'fs'

export default defineConfig({
    envDir: "../",
    base: '/panel/',
    plugins: [
        laravel({
            hotFile: '/home/pishkhanak/htdocs/pishkhanak.com/storage/panel.hot',
            buildDirectory: 'panel',
            input: [
                'resources/css/filament/access/theme.css',
                'resources/js/filament/access/app.js'
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'app/Filament/**',
            ],
        }),
        {
            name: 'fix-manifest-paths', // Custom plugin name
            closeBundle() {
                const manifestPath = path.resolve(__dirname, '/home/pishkhanak/htdocs/pishkhanak.com/public/panel/manifest.json');
                const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));

                // Modify the manifest paths to remove "../"
                for (const key in manifest) {
                    if (manifest.hasOwnProperty(key)) {
                        const newKey = key.replace(/^\.\.\//, ''); // Remove leading "../"
                        if (newKey !== key) {
                            manifest[newKey] = manifest[key];
                            delete manifest[key];
                        }
                        manifest[newKey].src = manifest[newKey].src.replace(/^\.\.\//, ''); // Remove "../" from src
                    }
                }

                // Write the updated manifest back to the file
                fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));
            }
        }
    ],
    resolve: {
        alias: {
            '@resources': path.resolve(__dirname, '../resources'),
        },
    },
    build: {
        outDir: '/home/pishkhanak/htdocs/pishkhanak.com/public/panel/',
        manifest: 'manifest.json',
        rollupOptions: {
            input: {
                theme: path.resolve(__dirname, '../resources/css/filament/access/theme.css'),
                app: path.resolve(__dirname, '../resources/js/filament/access/app.js'),
            }
        },
    },
})