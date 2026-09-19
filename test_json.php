<?php $j = json_decode(file_get_contents('storage/app/settings.json'), true); print_r(isset($j['dropdown_lists']) ? $j['dropdown_lists'] : 'NO_DROPDOWN_LISTS');
