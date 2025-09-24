// app/helpers/Bootstrap5Migration.php
class Bootstrap5Migration {

public static function updateClasses($content) {
$replacements = [
'text-muted' => 'text-body-secondary',
'ml-' => 'ms-',
'mr-' => 'me-',
'pl-' => 'ps-',
'pr-' => 'pe-',
'float-left' => 'float-start',
'float-right' => 'float-end',
'text-left' => 'text-start',
'text-right' => 'text-end',
'badge-primary' => 'bg-primary',
'badge-secondary' => 'bg-secondary',
];

foreach ($replacements as $old => $new) {
$content = str_replace($old, $new, $content);
}

return $content;
}

public static function addDarkModeSupport() {
return '<button class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()">
  <i class="fas fa-moon"></i>
</button>';
}
}