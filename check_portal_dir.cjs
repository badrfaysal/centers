const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\parent\\portal.blade.php', 'utf8');
const lines = content.split('\n');
const idx = lines.findIndex(l => l.includes('توجيهات الأخصائي'));
if (idx > -1) {
    console.log(lines.slice(idx - 10, idx + 10).join('\n'));
}