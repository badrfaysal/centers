import re

php = open('storage/framework/views/4128f7c725927010e802773cabbf7d4f.php', 'r', encoding='utf8').read()

blade = php
# <?php echo e(); ?>
blade = re.sub(r'<\?php echo e\((.*?)\); \?>', r'{{ \1 }}', blade)
blade = re.sub(r'<\?php echo \((.*?)\); \?>', r'{!! \1 !!}', blade)

# @section
blade = re.sub(r'<\?php \->startSection\((.*?),\s*(.*?)\); \?>', r'@section(\1, \2)', blade)
blade = re.sub(r'<\?php \->startSection\((.*?)\); \?>', r'@section(\1)', blade)
blade = re.sub(r'<\?php \->stopSection\(\); \?>', r'@endsection', blade)

# @if
blade = re.sub(r'<\?php if\((.*?)\): \?>', r'@if(\1)', blade)
blade = re.sub(r'<\?php elseif\((.*?)\): \?>', r'@elseif(\1)', blade)
blade = re.sub(r'<\?php else: \?>', r'@else', blade)
blade = re.sub(r'<\?php endif; \?>', r'@endif', blade)

# @foreach
blade = re.sub(r'<\?php \ = (.*?); \->addLoop\(\\); foreach\(\ as (.*?)\): \->incrementLoopIndices\(\); \ = \->getLastLoop\(\); \?>', r'@foreach(\1 as \2)', blade)
blade = re.sub(r'<\?php endforeach; \->popLoop\(\); \ = \->getLastLoop\(\); \?>', r'@endforeach', blade)

# @forelse
blade = re.sub(r'<\?php \\w+ = true; foreach\((.*?)\): \\w+ = false; \?>', r'@forelse(\1)', blade)
blade = re.sub(r'<\?php endforeach; if \(\\w+\): \?>', r'@empty', blade)
blade = re.sub(r'<\?php endif; \?>', r'@endforelse', blade)

# @php
blade = re.sub(r'<\?php\s+', r'@php ', blade)
blade = re.sub(r'\s+\?>', r' @endphp', blade)

with open('resources/views/doctor/index.blade.php', 'w', encoding='utf8') as f:
    f.write(blade)
print("Decompiled!")
