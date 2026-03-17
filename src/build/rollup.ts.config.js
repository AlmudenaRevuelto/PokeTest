import path from 'path';
import typescript from '@rollup/plugin-typescript';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
export default {
    input: path.resolve(__dirname, '../ts/main.ts'),
    output: {
        file: path.resolve(__dirname, '../../dist/js/main.js'),
        format: 'iife',   // <-- compatible navegador
        name: 'PokeTest'
    },
    plugins: [
        typescript({ tsconfig: path.resolve(__dirname, '../../tsconfig.json') })
    ]
};