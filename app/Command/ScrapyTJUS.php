<?php

declare(strict_types=1);

namespace App\Command;

use App\Scrapy\ScrapyJusSC;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

use function Hyperf\Coroutine\co;

#[Command]
class ScrapyTJUS extends HyperfCommand
{

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
        $this->line('Comando acionado!', 'info');
        $this->scrapy->scrapyJusSC([15, 16, 17, 18, 19, 20]);
    }
}
