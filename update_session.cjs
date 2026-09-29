const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\app\\Http\\Controllers\\DoctorSessionController.php';
let content = fs.readFileSync(path, 'utf8');

const target =         \\ = TherapySession::create(\\);\n        \\ = Child::findOrFail(\\['child_id']);;
const replacement =         \\ = TherapySession::create(\\);\n        \\ = Child::findOrFail(\\['child_id']);\n\n        if (!empty(\\['home_exercise'])) {\n            \\ = auth()->check() && auth()->user()->role === 'specialist' ? auth()->id() : null;\n            \\\\App\\\\Models\\\\Homework::create([\n                'child_id' => \\['child_id'],\n                'specialist_id' => \\,\n                'therapy_session_id' => \\->id,\n                'title' => 'واجب منزلي: ' . (\\['session_type'] ?? 'تخاطب'),\n                'description' => \\['home_exercise'],\n                'status' => 'pending'\n            ]);\n        };

if (content.includes(target)) {
    content = content.replace(target, replacement);
    fs.writeFileSync(path, content, 'utf8');
    console.log('Replaced successfully');
} else {
    const target2 =         \\ = TherapySession::create(\\);\r\n        \\ = Child::findOrFail(\\['child_id']);;
    if (content.includes(target2)) {
        content = content.replace(target2, replacement);
        fs.writeFileSync(path, content, 'utf8');
        console.log('Replaced successfully (CRLF)');
    } else {
        console.log('Target not found');
    }
}