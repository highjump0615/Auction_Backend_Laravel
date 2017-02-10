<?php

namespace App\Console\Commands;

use App\Model\Item;
use Illuminate\Console\Command;
use DateTime;
use DateInterval;

class TimeoutGiveupCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timeout:giveup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check timeout for determine give up';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        // automatic give up
        //
        $dateNow = new DateTime();

        // calculate the limit time from period
        $dateMin = new DateTime();
        $dateMin->sub(new DateInterval('P3D'));

        // query items for check 3 days max from end date
        // ---------------------
        // end_at < now < end_at + 3 dyas, i.e
        // now - 3 days < end_at < now
        // ---------------------
        $items = Item::where('end_at', '<', $dateNow)
            ->where('end_at', '>', $dateMin)
            ->where('contact', '>=', 0);

        foreach ($items as $item) {
            $dateMin = $item->end_at;

            // check each top bids for timeout
            foreach ($item->maxbid as $bid) {
                if ($bid->giveup_at) {
                    $dateMin = $bid->giveup_at;
                    continue;
                }

                if (dateDiffMin($dateNow, $dateMin) < 0) {
                    // time out, give up automatically
                    $bid->giveup_at = $dateNow;
                    $bid->save();
                }

                break;
            }
        }

        $strDate = $dateNow->format('Y-m-d H:i:s');
        $this->info("Giveup Cron is working: " . $strDate);
    }
}
