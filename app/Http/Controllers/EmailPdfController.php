<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Services\SimulateEmail;

class EmailPdfController extends Controller
{

    /**
     * Callback function of /generate-pdf url
     *
     * @return jsonResponse 
     */
    public function generate()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0);

        //Calling service method to read input file and simulate the email messages
        $emails = SimulateEmail::getTextOfEmailSimulation();

        //Loading Content in view and making PDF
        $html = View::make('pdf.email_thread', compact('emails'))->render();
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        //Stores Email at path app/private/
        $outputPath = 'email-thread.pdf';
        Storage::put($outputPath, $pdf->output());

        return response()->json(['message' => 'PDF generated successfully.', 'path' => "app/private"]);
    }
}
