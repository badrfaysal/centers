const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\app\\Http\\Controllers\\DashboardController.php', 'utf8');
const lines = content.split('\n');
const idx = lines.findIndex(l => l.includes('specialist_ready_alerts'));
if (idx > -1) {
    console.log(lines.slice(idx, idx + 30).join('\n'));
}