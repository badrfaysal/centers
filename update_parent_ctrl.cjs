const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\app\\Http\\Controllers\\ParentPortalController.php';
let content = fs.readFileSync(path, 'utf8');

const target =         \\ = \\->therapySessions()->latest('session_date')->get();;
const replacement =         \\ = \\->therapySessions()->latest('session_date')->get();\n        \\ = \\->homeworks()->latest()->get();;

if (content.includes(target)) {
    content = content.replace(target, replacement);
    
    const compactTarget = compact('child', 'sessions';
    const compactReplacement = compact('child', 'sessions', 'homeworks';
    content = content.replace(compactTarget, compactReplacement);
    
    fs.writeFileSync(path, content, 'utf8');
    console.log('Replaced successfully');
} else {
    console.log('Target not found');
}