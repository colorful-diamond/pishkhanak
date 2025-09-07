import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fg from 'fast-glob';

const exclude_files = [
    'resources/css/filament/access/theme.css',
    'resources/js/bootstrap.js',
    'resources/js/echo.js',
];

const entries_js = fg.sync(['resources/js/**/*.js']).reduce((acc, file) => {
    if(!exclude_files.includes(file)){
        const name = file.replace(/^resources\/js\//, '').replace(/\.js$/, '');
        acc[name + '_js'] = file;
    }
    return acc;
}, {});

const entries_css = fg.sync(['resources/css/**/*.css']).reduce((acc, file) => {
    if(!exclude_files.includes(file)){
        const name = file.replace(/^resources\/css\//, '').replace(/\.css$/, '');
        acc[name + '_css'] = file;
    }
    return acc;
}, {});


const custom_entries = {
    'app_js' : 'resources/js/app.js',
    'services_js' : 'resources/js/services.js',
};

const entries = { ...entries_js, ...entries_css, ...custom_entries };


console.log(entries);
export default defineConfig({
    plugins: [
        laravel({
            input: entries,
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});