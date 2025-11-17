import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import path from "path";
import fsExtra from 'fs-extra';
import { join } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        {
            // Plugin customizado para copiar assets estáticos
            name: 'copy-static-assets',
            writeBundle: async () => {
                const destDir = 'public/build/assets';
                
                // Copiar iconfonts
                const iconfontsSource = 'resources/assets/iconfonts';
                const iconfontsDest = join(destDir, 'iconfonts');
                if (await fsExtra.pathExists(iconfontsSource)) {
                    await fsExtra.copy(iconfontsSource, iconfontsDest, {
                        overwrite: true,
                        recursive: true,
                    });
                }
                
                // Copiar icons.css para iconfonts
                const iconsSource = 'resources/assets/css/icons.css';
                const iconsDest = join(destDir, 'iconfonts/icons.css');
                if (await fsExtra.pathExists(iconsSource)) {
                    await fsExtra.copy(iconsSource, iconsDest, {
                        overwrite: true,
                    });
                }
                
                // Copiar imagens
                const imagesSource = 'resources/assets/images';
                const imagesDest = join(destDir, 'images');
                if (await fsExtra.pathExists(imagesSource)) {
                    await fsExtra.copy(imagesSource, imagesDest, {
                        overwrite: true,
                        recursive: true,
                    });
                }
            },
        },
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '~': path.resolve(__dirname, './resources'),
        },
    },
    assetsInclude: ['**/*.woff', '**/*.woff2', '**/*.ttf', '**/*.eot', '**/*.svg', '**/*.otf'],
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    // Manter estrutura de fontes
                    if (assetInfo.name.match(/\.(woff|woff2|ttf|eot|otf)$/)) {
                        return 'assets/fonts/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                }
            }
        }
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'bukjob.sistema',
            protocol: 'ws',
            clientPort: 5173,
        },
    },
});
