const fs = require('fs');

const buf = fs.readFileSync('resources/views/doctor/index.blade.php');
// Let's check the first 100 chars as utf-8
const str = buf.toString('utf8');
console.log(str.substring(0, 100));
