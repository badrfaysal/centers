const fs = require('fs');
const path = 'd:\\Projects\\Centers\\Centers\\resources\\views\\layouts\\app.blade.php';
let content = fs.readFileSync(path, 'utf8');

const target = '        @endif\n    </script>';
const target2 = '        @endif\r\n    </script>';

const replacement = "        @endif\n\n        window.notifyAdminReady = function() {\n            Swal.fire({\n                title: 'هل أنت متأكد؟',\n                text: 'سيتم إرسال إشعار للإدارة بأنك جاهز لاستقبال الطفل التالي.',\n                icon: 'question',\n                showCancelButton: true,\n                confirmButtonColor: '#10b981',\n                cancelButtonColor: '#6b7280',\n                confirmButtonText: 'نعم، أنا جاهز',\n                cancelButtonText: 'إلغاء'\n            }).then((result) => {\n                if (result.isConfirmed) {\n                    fetch('{{ route(\"api.specialist-ready\") }}', {\n                        method: 'POST',\n                        headers: {\n                            'Content-Type': 'application/json',\n                            'X-CSRF-TOKEN': '{{ csrf_token() }}'\n                        },\n                        body: JSON.stringify({})\n                    })\n                    .then(res => res.json())\n                    .then(data => {\n                        if (data.status === 'success') {\n                            Swal.fire({\n                                title: 'تم بنجاح!',\n                                text: 'تم إرسال الإشعار للإدارة.',\n                                icon: 'success',\n                                confirmButtonText: 'حسناً',\n                                confirmButtonColor: '#10b981'\n                            });\n                        } else {\n                            Swal.fire('خطأ', 'حدث خطأ أثناء إرسال الإشعار', 'error');\n                        }\n                    })\n                    .catch(err => {\n                        console.error(err);\n                        Swal.fire('خطأ', 'حدث خطأ في الاتصال', 'error');\n                    });\n                }\n            });\n        };\n    </script>";

if (content.includes(target)) {
    content = content.replace(target, replacement);
    fs.writeFileSync(path, content, 'utf8');
    console.log("Replaced target1");
} else if (content.includes(target2)) {
    content = content.replace(target2, replacement);
    fs.writeFileSync(path, content, 'utf8');
    console.log("Replaced target2");
} else {
    console.log("Target not found");
}