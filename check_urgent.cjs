const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\app\\Http\\Controllers\\DashboardController.php', 'utf8');
const lines = content.split('\n');
const idx = lines.findIndex(l => l.includes('function getUrgentNotifications'));
if (idx > -1) {
    console.log(lines.slice(idx - 2, idx + 60).join('\n'));
}