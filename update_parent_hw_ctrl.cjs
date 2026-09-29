const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\app\\Http\\Controllers\\ParentHomeworkController.php';
let content = fs.readFileSync(path, 'utf8');

content = content.replace(
    /\\ = \\->homeworks\(\)->latest\(\)->paginate\(15\);/,
    \\ = \\->homeworks()->latest()->get();
);

fs.writeFileSync(path, content, 'utf8');