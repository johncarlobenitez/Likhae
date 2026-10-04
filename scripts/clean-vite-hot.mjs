import { rmSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const hotFile = fileURLToPath(new URL('../public/hot', import.meta.url));

rmSync(hotFile, { force: true });
