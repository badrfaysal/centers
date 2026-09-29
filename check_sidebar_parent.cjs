const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', 'utf8');
const lines = content.split('\n');
console.log(lines.slice(290, 310).join('\n'));