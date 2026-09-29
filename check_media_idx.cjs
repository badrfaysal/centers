const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', 'utf8');
const lines = content.split('\n');
const idx = lines.findIndex(l => l.includes('media.index'));
if (idx > -1) {
    console.log(lines.slice(idx - 5, idx + 5).join('\n'));
}