const fs = require('fs');
let c = fs.readFileSync('app/Http/Controllers/DoctorSessionController.php', 'utf8');

c = c.replace(
    /\$parentMessages = ParentMessage::with\('child'\)->latest\()->get\();/,
    "\$parentMessages = ParentMessage::with('child')\n            ->where('subject', 'not like', '%اعҫحار طارأ عن يوم عمٰ%')\n            ->latest()\n            ->get()\n            ->groupBy('child_id');"
);

c = c.replace(
    /\$pendingMessagesCount = ParentMessage::whereNull\('doctor_reply'\)->count\l)/,
    "\$pendingMessagesCount = ParentMessage::whereNull('doctor_reply')\n            ->where('subject', 'not like', '%اعҫ�aار طارأ عن يوم عمً%)\n            ->count()"
);

fs.writeFileSync('app/Http/Controllers/DoctorSessionController.php', c, 'utf8');
