<?php
add_filter('upload_mimes', function ($mimes) {
    $mimes['step']   = 'model/step';
    $mimes['stp']    = 'model/step';
    $mimes['igs']    = 'model/iges';
    $mimes['iges']   = 'model/iges';
    $mimes['sldprt'] = 'application/sldprt';
    $mimes['prt']    = 'application/octet-stream';
    return $mimes;
});
add_filter('mime_types', function ($mimes) {
    $mimes['step']   = 'model/step';
    $mimes['stp']    = 'model/step';
    $mimes['igs']    = 'model/iges';
    $mimes['iges']   = 'model/iges';
    $mimes['sldprt'] = 'application/sldprt';
    $mimes['prt']    = 'application/octet-stream';
    return $mimes;
});
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $cad = ['step', 'stp', 'igs', 'iges', 'sldprt', 'prt'];
    if (in_array($ext, $cad)) {
        $data['ext']  = $ext;
        $data['type'] = $mimes[$ext] ?? 'application/octet-stream';
        $data['proper_filename'] = false;
    }
    return $data;
}, 10, 4);
