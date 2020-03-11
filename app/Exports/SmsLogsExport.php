<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SmsLogsExport implements FromCollection, WithHeadings
{

    use Exportable;

    private $after; // string with format 2019-01-29
    private $before; // string with format 2019-01-29
    private $client; //twilio client
    private $output;

    public function __construct($twilioClient, $output, string $after, string $before) 
    {
        $this->client = $twilioClient;
        $this->after = $after;
        $this->before = $before;
        $this->output = $output;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        // get the total count
        // https://www.twilio.com/docs/usage/api/usage-record?code-sample=code-last-months-usage-for-all-usage-categories-4&code-language=PHP&code-sdk-version=5.x
        $records = $this->client->usage->records->read(
            array(
                "category" => "sms",
                "startDate" => $this->after, 
                "endDate" => $this->before
            )
        );
        // (should only be 1 since "sms" is the set category)
        foreach ($records as $record) {
            $count = $record->count;
        }
        $this->output->title('Total Records for the export: ' . $count);
        
        $bar = $this->output->createProgressBar($count);

        $messages = $this->client->messages->stream(
            array( 
            'dateSentAfter' => $this->after, 
            'dateSentBefore' => $this->before
            )
          );

          
        

        /* Write rows */
        $rows = array();
        foreach ($messages as $sms) { 
            $rows[] = array(
                $sms->sid,
                $sms->from,
                $sms->to,
                $sms->dateSent->format('Y-m-d H:i:s'),
                $sms->status,
                $sms->direction,
                $sms->price,
                $sms->body
            );
            $bar->advance();
        }

        //
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'SMS Message SID', 
            'From',
            'To', 
            'Date Sent', 
            'Status', 
            'Direction', 
            'Price', 
            'Body'
        ];
    }
}
