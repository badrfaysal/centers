const fs = require('fs');
let str = fs.readFileSync('storage/framework/views/4128f7c725927010e802773cabbf7d4f.php', 'utf8');

str = str.replace(/<\?php echo e\((.*?)\); \?>/g, "{{ $1 }}");
str = str.replace(/<\?php echo \((.*?)\); \?>/g, "{!! $1 !!}");
str = str.replace(/<\?php \$__env->startSection\('(.*?)', (.*?)\); \?>/g, "@section('$1', $2)");
str = str.replace(/<\?php \$__env->startSection\('(.*?)'\); \?>/g, "@section('$1')");
str = str.replace(/<\?php \$__env->stopSection\(\); \?>/g, "@endsection");
str = str.replace(/<\?php if\((.*?)\): \?>/g, "@if($1)");
str = str.replace(/<\?php elseif\((.*?)\): \?>/g, "@elseif($1)");
str = str.replace(/<\?php else: \?>/g, "@else");
str = str.replace(/<\?php endif; \?>/g, "@endif");
str = str.replace(/<\?php \$__currentLoopData = (.*?); \$__env->addLoop\(\$__currentLoopData\); foreach\(\$__currentLoopData as (.*?)\): \$__env->incrementLoopIndices\(\); \$loop = \$__env->getLastLoop\(\); \?>/g, "@foreach($1 as $2)");
str = str.replace(/<\?php endforeach; \$__env->popLoop\(\); \$loop = \$__env->getLastLoop\(\); \?>/g, "@endforeach");
str = str.replace(/<\?php \$__empty_.* = true; foreach\((.*?)\): \$__empty_.* = false; \?>/g, "@forelse($1)");
str = str.replace(/<\?php endforeach; if \(\$__empty_.*\): \?>/g, "@empty");
str = str.replace(/<\?php\s+/g, "@php ");
str = str.replace(/\s+\?>/g, " @endphp");

fs.writeFileSync('resources/views/doctor/index.blade.php', str);
console.log("Decompiled!")
