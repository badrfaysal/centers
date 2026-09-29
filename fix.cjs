const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php';
let content = fs.readFileSync(path, 'utf8');

const badBtn = '<button type="button" onclick="window.notifyAdminReady()" class="mt-4 flex w-full items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 group">';
const badSpan = '<span class="font-extrabold text-xs">أنا جاهز (إبلاغ الإدارة)</span>';

if (content.includes(badBtn)) {
    content = content.replace(badBtn, '<button type="button" onclick="window.notifyAdminReady()" class="mt-4 flex w-full items-center gap-3.5 py-3 rounded-2xl transition bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 group" :class="sidebarCollapsed ? \'justify-center px-0\' : \'px-3.5\'">');
    content = content.replace(badSpan, '<span x-show="!sidebarCollapsed" x-transition class="font-extrabold text-xs">أنا جاهز (إبلاغ الإدارة)</span>');
    fs.writeFileSync(path, content, 'utf8');
    console.log("Fixed button styles");
} else {
    console.log("Could not find button to fix");
}