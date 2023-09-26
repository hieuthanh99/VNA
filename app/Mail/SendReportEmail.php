<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Helpers\Helper;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use PDF;
use Carbon\Carbon;

class SendReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdfData)
    {
        $this->pdfData = $pdfData;
    }

    public function build()
    {
       // dd("dsadasdsa");
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($this->pdfData); // Dữ liệu HTML để tạo PDF

        // // Tuỳ chỉnh PDF tùy ý (nếu cần)
        // $dompdf->setPaper('A4', 'portrait');

        // // Render PDF
        // $dompdf->render();

        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y')
        ];
        $dataEmail = Helper::reportWeeked();
        $record = $dataEmail['record'];
        // dd($record);
        if(!empty($record)) {
            $dataDate =  $record->date_start;
            $dateCarbon = Carbon::parse($dataDate);
            $dayOfWeek = Carbon::parse($record->date_start)->dayOfWeek;
            if ($dayOfWeek > 5) {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek - 5)->format('d-m-Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek + 7)->format('d-m-Y');
            } else {
                $startDateOfWeekInput = $dateCarbon->copy()->subDays($dayOfWeek + 6 - 4)->format('d-m-Y');
                $endDateOfWeekInput = $dateCarbon->copy()->addDays(4 - $dayOfWeek)->format('d-m-Y');
            }
        }
        //dd($dataEmail)
        $pdf = PDF::loadView('pdf.template', ['department' => $dataEmail['mergedArray'],'startDateOfWeekInput' => $startDateOfWeekInput, "endDateOfWeekInput" => $endDateOfWeekInput]);
        // Lưu tệp PDF vào bộ nhớ tạm thời
        $pdfContent = $pdf->output();
        $pdfPath = storage_path('report.pdf');
        file_put_contents($pdfPath, $pdfContent);
       // dd($pdfPath);
        // Đính kèm tệp PDF vào email
        return $this->from('n.hieuthanhps@gmail.com')->view('email.email_report')
                    ->attach($pdfPath, [
                        'as' => 'report.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Send Report Email',
        );
    }

    // /**
    //  * Get the message content definition.
    //  *
    //  * @return \Illuminate\Mail\Mailables\Content
    //  */
    public function content()
    {
        return new Content(
            view: 'email.email_report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
