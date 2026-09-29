const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php';
let content = fs.readFileSync(path, 'utf8');

const regex = /let isSpecialistReady = latest.*?Swal\.fire\(\{(.*?)\}\)/s;
const newLogic = fs.readFileSync('logic.txt', 'utf8');

if (content.match(regex)) {
    content = content.replace(regex, newLogic);
    fs.writeFileSync(path, content, 'utf8');
    console.log('Replaced successfully');
} else {
    console.log('Regex did not match');
}