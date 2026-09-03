const fs = require('fs');
const str = fs.readFileSync('resources/views/doctor/index.blade.php', 'utf8');
let res = [];
for(let i = 0; i < str.length; i++){
    if(str.charCodeAt(i) > 127){
        res.push(str.charCodeAt(i).toString(16));
        if(res.length >= 20) break;
    }
}
console.log(res.join(' '));
