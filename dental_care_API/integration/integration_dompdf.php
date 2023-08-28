<?php
namespace gtc_core;

require_once('../integration/vendor/autoload.php');

use Dompdf\Dompdf;
use Dompdf\Options;

try {

    $options = new Options();
    $options->setChroot('');
    $dompdf = new DOMPDF($options);

    //$dompdf = new DOMPDF();


} catch (\Exception $e) {
    //silent exception
}


?>