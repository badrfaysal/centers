const fs = require('fs');

let childPath = 'app\\\\Models\\\\Child.php';
let childContent = fs.readFileSync(childPath, 'utf8');
if (!childContent.includes('public function homeworks()')) {
    childContent = childContent.replace(/}\s*$/, \n    public function homeworks()\n    {\n        return \\->hasMany(Homework::class);\n    }\n}\n);
    fs.writeFileSync(childPath, childContent, 'utf8');
}

let tsPath = 'app\\\\Models\\\\TherapySession.php';
let tsContent = fs.readFileSync(tsPath, 'utf8');
if (!tsContent.includes('public function homeworks()')) {
    tsContent = tsContent.replace(/}\s*$/, \n    public function homeworks()\n    {\n        return \\->hasMany(Homework::class);\n    }\n}\n);
    fs.writeFileSync(tsPath, tsContent, 'utf8');
}