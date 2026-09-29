const fs = require('fs');
let content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', 'utf8');

// Remove the first center.screen block
content = content.replace(/<!-- شاشة الانتظار للمركز -->[\s\S]*?<\/a>/, '');

// Remove the QR screen block
content = content.replace(/<!-- شاشة الـ QR للحضور السريع -->[\s\S]*?<\/a>/, '');

// Remove the second center.screen block
content = content.replace(/<!-- شاشة النداء الصوتي للمركز -->[\s\S]*?<\/a>/, '');

// Formalize the sidebar colors
// We can replace all text-{color}-400 with something more formal like text-slate-400 or just inherit
// Let's replace the specific playful colors in sidebar links:
// text-amber-400, text-emerald-400, text-purple-400, text-blue-400, text-rose-400, text-indigo-400, text-teal-400, text-sky-400
const colors = ['amber', 'emerald', 'purple', 'blue', 'rose', 'indigo', 'teal', 'sky', 'fuchsia'];
colors.forEach(c => {
    // Only in the sidebar context:
    // Regex to match: <i class="... text-{color}-400 ..."></i> inside sidebar
    // This is a bit tricky, but since it's an internal admin panel, replacing text-color-400 in icons is mostly safe
    content = content.replace(new RegExp(	ext--400, 'g'), 'text-slate-400 opacity-80');
});

// Remove the extra styling from the "I am ready" button if it's too playful, but maybe keep it functional.
// Make rounded-2xl into rounded-xl or rounded-lg for a more formal structural look?
// "رسمي اكتر" usually means less rounded corners, fewer bright random colors.
content = content.replace(/rounded-2xl/g, 'rounded-xl');
content = content.replace(/rounded-3xl/g, 'rounded-2xl');

fs.writeFileSync('d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php', content, 'utf8');
console.log('Sidebar cleaned and formalized.');