<?php

namespace App\Console\Commands;

use App\Services\Parser\BethovenParser;
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
        $parseList = [
            'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm-dlya-sobak-duke-s-farm-dlya-srednikh-i-krupnykh-porod-indeyka-sukh-2kg/',
            'https://www.bethowen.ru/catalogue/cats/korma/vlazhnye-korma/korm-dlya-koshek-royal-canin-royal-kanin-ultra-light-dlya-koshek-sklonnykh-k-polnote-v-zhele-kons-85/',
            'https://www.bethowen.ru/catalogue/dogs/korma/syxoi/korm-dlya-sobak-royal-canin-royal-kanin-german-shepherd-24-dlya-porody-nemetskaya-ovcharka-starshe-1/',
        ];

        //проверять не спаршен ли товар
        foreach ($parseList as $page) {
            $parser = new BethovenParser($page);
            $parser->parseProduct();
            $parser->saveParsed();
        }
    }
}
