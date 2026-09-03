const fs = require('fs');
const str = fs.readFileSync('resources/views/doctor/index.blade.php', 'utf8');
const part = str.substring(32, 40);
console.log(part.split('').map(c => c.charCodeAt(0).toString(16)).join(' '));
