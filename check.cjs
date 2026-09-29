const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php';
let content = fs.readFileSync(path, 'utf8');

const targetRegex = /let isDayApology(.*?)(Swal\.fire\(\{(.*?)\}\)\.then\(\(result\) => \{)/s;
const match = content.match(targetRegex);
if(match) {
    console.log('Found match!');
} else {
    console.log('Match not found');
}