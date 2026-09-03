const fs = require('fs');

let c = fs.readFileSync('resources/views/doctor/index.blade.php', 'utf8');

const start_str = '<div x-show="activeTab === \'messages\'" class="space-y-6">';

fs.writeFileSync('resources/views/doctor/index.blade.php', c.substring(0, c.indexOf(start_str)), 'utf8');
