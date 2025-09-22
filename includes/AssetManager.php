<?php
namespace App\Helpers;
/** Simple Asset Manager for registering logical bundles from views. */
class AssetManager
{
    private static array $css = [];
    private static array $js = [];
    private static array $bundles = [
        'datatables' => [
            'css' => [
                'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css',
                'https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css',
                'https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css'
            ],
            'js' => [
                'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
                'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js',
                'https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js',
                'https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js',
                'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js',
                'https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
                'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
                'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js',
                'https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js',
                'https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js'
            ]
        ],
        'select2' => [
            'css' => [ 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', 'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css' ],
            'js'  => [ 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' ]
        ],
        'summernote' => [
            'css' => [ 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css' ],
            'js'  => [ 'https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js' ]
        ],
        'chartjs' => [
            'css' => [],
            'js'  => [ 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js' ]
        ]
        ,
        'fullcalendar' => [
            'css' => [ 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' ],
            'js'  => [
                'https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js',
                'https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.js',
                'https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/main.min.js',
                'https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.10/main.min.js'
            ]
        ],
        'home' => [
            'css' => [],
            'js'  => []
        ]
    ];
    public static function addBundle(string $name): void
    {
        if(isset(self::$bundles[$name])) {
            self::$css = array_merge(self::$css, self::$bundles[$name]['css']);
            self::$js  = array_merge(self::$js, self::$bundles[$name]['js']);
        }
    }
    public static function addCss(string $path): void { self::$css[] = $path; }
    public static function addJs(string $path): void { self::$js[] = $path; }
    private static function unique(array $list): array { return array_values(array_unique($list)); }
    public static function renderCss(string $baseUrl): string
    {
        $out=[];
        foreach(self::unique(self::$css) as $c){
            $url = (strpos($c, 'http') === 0) ? $c : $baseUrl . $c;
            $out[]="<link rel=\"stylesheet\" href=\"{$url}\" />";
        }
        return implode("\n",$out);
    }
    public static function renderJs(string $baseUrl): string
    {
        $out=[];
        foreach(self::unique(self::$js) as $j){
            $url = (strpos($j, 'http') === 0) ? $j : $baseUrl . $j;
            $out[]="<script src=\"{$url}\"></script>";
        }
        return implode("\n",$out);
    }
}
?>
