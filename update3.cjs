const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php';
let content = fs.readFileSync(path, 'utf8');

const regex = /let isDayApology(.*?)(Swal\.fire\(\{(.*?)\}\))/s;

let newLogic = `let isSpecialistReady = latest.is_specialist_ready === true;
                                let isDayApology = latest.title && latest.title.includes('اعتذار طارئ عن يوم عمل');
                                let isSpecialistSessionApology = latest.title && latest.title.includes('اعتذار طارئ للأخصائي عن الجلسة');
                                let headerHtml = '';
                                
                                if (isSpecialistReady) {
                                    let specName = latest.sender;
                                    headerHtml = \`<div style="background-color: #d1fae5; color: #065f46; padding: 20px; border-radius: 15px; margin-bottom: 20px; font-size: 1.6em; font-weight: 900; border: 2px dashed #10b981; display:flex; align-items:center; justify-content:center; gap:15px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);"><i class="fa-solid fa-bell-concierge fa-bounce text-emerald-500 text-3xl"></i> الأخصائي: <span style="color: #047857;">${specName}</span> جاهز لدخول الطفل القادم</div>\`;
                                } else if (isDayApology) {
                                    let specName = latest.sender.replace('الأخصائي: ', '');
                                    headerHtml = \`<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 1.8em; font-weight: bold; border: 2px solid #f5c6cb;">الأخصائي: <span style="color: #d9534f;">${specName}</span></div>\`;
                                } else if (isSpecialistSessionApology) {
                                    let specName = latest.sender; 
                                    headerHtml = \`<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 1.8em; font-weight: bold; border: 2px solid #f5c6cb;">عاجل - اعتذار أخصائي عن جلسة<br><span style="font-size: 0.8em; color: #d9534f;">الطفل: ${latest.child_name || ''}</span></div>\`;
                                } else {
                                    headerHtml = \`<div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 1.8em; font-weight: bold; border: 2px solid #ffeeba;">الطفل: <span style="color: #d9534f;">${latest.child_name || ''}</span></div>\`;
                                }

                                if (isSpecialistReady) {
                                    if (window.playNotificationBeep) {
                                        window.playNotificationBeep();
                                    } else {
                                        let audio = new Audio('https://actions.google.com/sounds/v1/alarms/beep_short.ogg');
                                        audio.play().catch(e => console.log(e));
                                    }
                                }

                                Swal.fire({
                                    icon: isSpecialistReady ? 'info' : 'warning',
                                    title: isSpecialistReady ? 'إشعار جاهزية الأخصائي' : 'إشعار عاجل!',
                                    html: \`
                                        ${headerHtml}
                                        ${!isSpecialistReady ? \`<span style="font-size: 1.2em; color: #333; font-weight: bold;">${latest.title}</span><br><br>\` : ''}
                                        ${!isSpecialistReady ? \`<span style="font-size: 1.1em; color: #555;">${latest.body}</span><br>\` : ''}
                                        ${latest.affected_html || ''}
                                        <br>
                                        <small style="color: #777;">تم الإرسال بواسطة: ${latest.sender}</small>
                                    \`,
                                    showDenyButton: !isSpecialistReady,
                                    showCancelButton: true,
                                    confirmButtonText: 'حسناً، فهمت',
                                    denyButtonText: isDayApology ? 'نقل الجلسات لأخصائي آخر' : (isSpecialistSessionApology ? 'استبدال بأخصائي آخر' : 'تسكين طفل آخر'),
                                    cancelButtonText: 'ذكرني لاحقاً',
                                    confirmButtonColor: '#0d9488',
                                    denyButtonColor: '#f59e0b',
                                    cancelButtonColor: '#64748b',
                                    width: '600px',
                                    backdrop: \`rgba(0,0,0,0.6)\`
                                })`;

if (content.match(regex)) {
    content = content.replace(regex, newLogic);
    fs.writeFileSync(path, content, 'utf8');
    console.log('Replaced successfully');
} else {
    console.log('Regex did not match');
}