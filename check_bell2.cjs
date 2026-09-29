const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', 'utf8');
const lines = content.split('\n');
const idx = lines.findIndex(l => l.includes('fa-bell') && !l.includes('concierge'));
if (idx > -1) {
    console.log(lines.slice(idx - 15, idx + 25).join('\n'));
} else {
    console.log('Not found');
}