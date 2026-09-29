const fs = require('fs');
const content = fs.readFileSync('d:\\Projects\\Centers\\Centers\\app\\Http\\Controllers\\DashboardController.php', 'utf8');
const lines = content.split('\n');

const startIndex = lines.findIndex(l => l.includes('foreach ( as ) {'));
if (startIndex !== -1) {
    console.log(lines.slice(startIndex - 5, startIndex + 15).join('\n'));
}

const activeIndex = lines.findIndex(l => l.includes(' ='));
if (activeIndex !== -1) {
    console.log('\n--- Active ---');
    console.log(lines.slice(activeIndex - 2, activeIndex + 5).join('\n'));
}