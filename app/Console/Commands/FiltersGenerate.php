<?php

namespace App\Console\Commands;

use App\Models\Shop\Sections;
use App\Services\Shop\FiltersGeneratorService;
use App\Services\Shop\GoodsStorage;
use App\Services\Shop\SearchService;
use Illuminate\Console\Command;

class FiltersGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filters:generate {--section=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $s = new SearchService();
        $res = $s->find('Корм');
        dd($res);

        $sections = $this->option('section');

        $goodsStorage = new GoodsStorage();
        $FilterGenerateService = new FiltersGeneratorService($goodsStorage);
        if (count($sections)) {
            foreach ($sections as $section_id) {
                $FilterGenerateService->generateForSection($section_id);
                $FilterGenerateService->fillFilterCombinations($section_id);
            }
        } else {
            $sections = Sections::all();
            foreach ($sections as $section) {
                $FilterGenerateService->generateForSection($section->id);
                $FilterGenerateService->fillFilterCombinations($section->id);
            }
        }
    }
}
