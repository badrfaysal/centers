const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php';
let content = fs.readFileSync(path, 'utf8');

const badBtn = '<button type="button" onclick="window.notifyAdminReady()" class="mt-4 flex w-full items-center gap-3.5 py-3 rounded-2xl transition bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 group" :class="sidebarCollapsed ? \'justify-center px-0\' : \'px-3.5\'">';
const goodBtn = '<button type="button" onclick="window.notifyAdminReady()" class="mt-4 flex w-full items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 group">';

const badSpan = '<span x-show="!sidebarCollapsed" x-transition class="font-extrabold text-xs">أنا جاهز (إبلاغ الإدارة)</span>';
const goodSpan = '<span class="font-extrabold text-xs">أنا جاهز (إبلاغ الإدارة)</span>';

if (content.includes(badBtn) || content.includes(badSpan)) {
    content = content.replace(badBtn, goodBtn);
    content = content.replace(badSpan, goodSpan);
    fs.writeFileSync(path, content, 'utf8');
    console.log("Fixed button");
} else {
    console.log("Could not find button to fix");
}