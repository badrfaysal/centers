<?php
$lines = file("resources/views/website/index.blade.php");
// Find where the foreach ends (should be around line 513 now)
for ($i=505; $i<520; $i++) {
    if (strpos($lines[$i], "@endforeach") !== false) {
        // We need to insert </select> and </div> right after this line
        array_splice($lines, $i+1, 0, [
            "                            </select>\n",
            "                        </div>\n\n"
        ]);
        break;
    }
}
file_put_contents("resources/views/website/index.blade.php", implode("", $lines));
echo "Done";

