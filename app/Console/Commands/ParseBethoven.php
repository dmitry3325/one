<?php

namespace App\Console\Commands;

use App\Services\Parser\BethovenParser;
use DB;
use Illuminate\Console\Command;

class ParseBethoven extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parseBethoven';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'парсим бетховен :)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $maxCycleCnt = 11;

        //readed file 1
        for ($i = 1; $i < $maxCycleCnt; $i++) {
            $file      = file_get_contents('./test/' . $i);
            $parseList = explode(PHP_EOL, $file);

            //            $parseList = [
            ////                'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm-dlya-sobak-duke-s-farm-dlya-srednikh-i-krupnykh-porod-indeyka-sukh-2kg/',
            ////                'https://www.bethowen.ru/catalogue/cats/korma/vlazhnye-korma/korm-dlya-koshek-royal-canin-royal-kanin-ultra-light-dlya-koshek-sklonnykh-k-polnote-v-zhele-kons-85/',
            ////                'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm-dlya-sobak-royal-canin-royal-kanin-german-shepherd-24-dlya-porody-nemetskaya-ovcharka-starshe-1/',
            //                'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm_dlya_sobak_happy_dog_fit_well_senor_dlya_pozhilykh_sobak_ptitsa_losos_yagnenok_yaytsa_sukh_4kg/',
            //                'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm_dlya_shchenkov_best_choice_junior_dlya_melkikh_i_srednikh_porod_kuritsa_kukuruza_ris_sukh_4kg/'
            //            ];

            //проверять не спаршен ли товар
            foreach ($parseList as $num => $page) {
                if ($page) {
                    $p = DB::table('shop.parse')->where('url', $page)->count();
                    if (!$p) {
                        try {
                            $parser = new BethovenParser($page);
                            $parser->parseProduct();
                            $parser->saveParsed();
                            echo $num . ' ' . $page . PHP_EOL;
                            DB::table('shop.parse')->insert([
                                'url' => $page,
                                'success' => true
                            ]);
                        }
                        catch (\Exception $er) {
                            DB::table('shop.parse')->insert([
                                'url' => $page,
                                'success' => false
                            ]);
                            echo $num . ' ' . $page . ' error ' . $er->getMessage() . PHP_EOL;
                        }
                    }
                }
            }
        }
    }
}
