<?php

declare(strict_types=1);

namespace App\Command;

use App\Scrapy\ScrapyJusSC;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class ScrapyTJUS extends HyperfCommand
{
    /**
     * The command
     *
     * @var int
     */
    protected ?int $numberPages = 10;

    public function __construct(protected ContainerInterface $container, protected ScrapyJusSC $scrapy)
    {
        parent::__construct('scrapy:scjus');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Scrapy website https://busca.tjsc.jus.br/jurisprudencia');
    }

    public function handle()
    {
        $this->numberPages = (int) $this->input->getArgument('numberPages');
        $pags = [];
        for ($i = 1; $i <= $this->numberPages; $i++) { 
            $pags[] = $i;
        }
        $this->line('Comando acionado!', 'info');
        $this->scrapy->scrapyJusSC($pags);
    }

    protected function getArguments()
    {
        return [
            ['numberPages', InputArgument::OPTIONAL]
        ];
    }
}
