<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $event = \App\Models\Event::first();
    $letter = \App\Models\EventLetter::latest()->first();
    $orgSettings = \App\Models\AppSetting::getOrg();
    
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('events.letters.pdf', compact('event', 'letter', 'orgSettings'));
    $content = $pdf->output();
    echo "PDF generated successfully, size: " . strlen($content) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
}
