<?php

use App\Models\AppSetting;
use App\Models\Event;
use App\Models\EventLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $event = Event::first();
    $letter = EventLetter::latest()->first();
    $orgSettings = AppSetting::getOrg();

    $pdf = Pdf::loadView('events.letters.pdf', compact('event', 'letter', 'orgSettings'));
    $content = $pdf->output();
    echo 'PDF generated successfully, size: '.strlen($content)." bytes\n";
} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
    echo 'FILE: '.$e->getFile().' LINE: '.$e->getLine()."\n";
}
