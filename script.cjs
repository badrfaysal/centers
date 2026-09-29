
const fs = require("fs");
const path = "d:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php";
let content = fs.readFileSync(path, "utf8");

const target = "                                <span>الملفات والفيديوهات</span>\n                            </a>";
const target2 = "                                <span>الملفات والفيديوهات</span>\r\n                            </a>";

const replacement = "                                <span>الملفات والفيديوهات</span>\n                            </a>\n                            <!-- زر أنا جاهز لدخول الطفل التالي -->\n                            <button type=\"button\" onclick=\"window.notifyAdminReady()\" class=\"mt-4 flex w-full items-center gap-3.5 px-3.5 py-3 rounded-2xl transition bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 group\">\n                                <div class=\"relative flex items-center justify-center\">\n                                    <span class=\"absolute w-full h-full rounded-full bg-emerald-500 opacity-20 group-hover:animate-ping\"></span>\n                                    <i class=\"fa-solid fa-bell-concierge w-5 text-center text-base\"></i>\n                                </div>\n                                <span class=\"font-extrabold text-xs\">أنا جاهز (إبلاغ الإدارة)</span>\n                            </button>";

if (content.includes(target)) {
    content = content.replace(target, replacement);
    fs.writeFileSync(path, content, "utf8");
    console.log("Replaced target1");
} else if (content.includes(target2)) {
    content = content.replace(target2, replacement);
    fs.writeFileSync(path, content, "utf8");
    console.log("Replaced target2");
} else {
    console.log("Target not found");
}
