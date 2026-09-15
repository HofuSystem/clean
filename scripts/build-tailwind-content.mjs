import fs from 'node:fs';
import path from 'node:path';

const roots = [
  ['resources', ['.blade.php', '.js', '.vue']],
  ['packages', ['.blade.php']],
  ['vendor/laravel/framework/src/Illuminate/Pagination/resources/views', ['.blade.php']],
];
const chunks = [];
function collect(dir, extensions) {
  if (!fs.existsSync(dir)) return;
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const file = path.join(dir, entry.name);
    if (entry.isDirectory()) collect(file, extensions);
    else if (extensions.some((ext) => file.endsWith(ext))) chunks.push(fs.readFileSync(file, 'utf8'));
  }
}
for (const [root, extensions] of roots) collect(root, extensions);
fs.mkdirSync('storage/framework', { recursive: true });
fs.writeFileSync('storage/framework/tailwind-content.html', chunks.join('\n'), 'utf8');
console.log(`Tailwind content snapshot: ${chunks.length} files`);