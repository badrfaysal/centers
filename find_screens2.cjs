const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', 'utf8');
const lines = content.split('\n');
const centerScreenIdx = lines.findIndex(l => l.includes('route(\'attendance.screen\')'));
if (centerScreenIdx > -1) {
    console.log(lines.slice(centerScreenIdx - 5, centerScreenIdx + 15).join('\n'));
}