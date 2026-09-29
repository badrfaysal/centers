const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', 'utf8');
const lines = content.split('\n');
const start = lines.findIndex(l => l.includes('<header'));
if (start > -1) {
    console.log(lines.slice(start, start + 30).join('\n'));
}