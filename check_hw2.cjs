const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\parent\\portal.blade.php', 'utf8');
const lines = content.split('\n');
console.log(lines.slice(717, 750).join('\n'));