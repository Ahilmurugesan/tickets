<?php

namespace Tests\Browser;

use App\Mail\IPLMail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Vonage\Laravel\Facade\Vonage;
use Vonage\Voice\NCCO\NCCO;

class IplTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            try {

                $browser->visit('https://insider.in/go/chennai-super-kings-2023')
                    ->assertSee('Nothing To See Here');

                $browser->visit('https://insider.in/go/chennai-super-kings-2023-home-matches')
                    ->assertSee('Nothing To See Here');

                $browser->visit('https://insider.in/indian-premier-league-ipl/article')
                    ->click('a[href="/go/chennai-super-kings-2023"]')
                    ->assertSee('Nothing To See Here');

            } catch (\PHPUnit\Framework\ExpectationFailedException $e) {

                $keypair = new \Vonage\Client\Credentials\Keypair(
                   file_get_contents(base_path('private.key')),
                    "e64fd9fc-60e5-4301-a190-0cabef6e2a0f"
                );
                $client = new \Vonage\Client($keypair);

                $outboundCall = new \Vonage\Voice\OutboundCall(
                    new \Vonage\Voice\Endpoint\Phone("919791537882"),
                    new \Vonage\Voice\Endpoint\Phone("919791537882")
                );
                $ncco = new NCCO();
                $ncco->addAction(new \Vonage\Voice\NCCO\Action\Talk('IPL Tickets'));
                $outboundCall->setNCCO($ncco);

                $response = $client->voice()->createOutboundCall($outboundCall);

                Mail::to('ahilmurugesan@gmail.com')
                    ->send(new IPLMail());

                $basic  = new \Vonage\Client\Credentials\Basic("e86baf62", "2UBApyTozHrYmUtd");
                $client = new \Vonage\Client($basic);

                $response = $client->sms()->send(
                    new \Vonage\SMS\Message\SMS("919791537882", 'BRAND_NAME', 'IPL Tickets')
                );

                $message = $response->current();

                if ($message->getStatus() == 0) {
                    dd("The message was sent successfully\n");
                } else {
                    dd("The message failed with status: " . $message->getStatus() . "\n");
                }
            }
        });
    }
}
