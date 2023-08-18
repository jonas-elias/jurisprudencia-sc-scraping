<?php

namespace App\Scrapy\Trait;

/**
 * trait FormatScrapy
 *
 * @author <jonas-elias/>
 */
trait FormatScrapy
{
    /**
     * @var array $especialItens
     */
    private array $especialItens = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ã' => 'a',
        'õ' => 'o',
        '~' => '',
    ];

    /**
     * Method to format paragrafo
     *
     * @param string $paragrafo
     * @return string
     */
    public function formatParagrafo(string $paragrafo): string
    {
        return trim(preg_replace('/\s+/', ' ', mb_convert_encoding($paragrafo, 'utf-8')));
    }

    /**
     * Method to format chave cabecalho
     *
     * @param string $atributoCabecalho
     * @return string
     */
    public function formatChave(string $atributoCabecalho): string
    {
        return strtolower(str_replace(" ", "_", str_replace(':', '', strtr($atributoCabecalho, $this->especialItens))));
    }

    /**
     * Method to format node cabecalho
     *
     * @param string $nodeCabecalho
     * @return string
     */
    public function formatNode(string $nodeCabecalho): string
    {
        return str_replace(array("\r", "\n", "\t"), '', trim($nodeCabecalho));
    }

    /**
     * Method to wait next request
     *
     * @param int $seconds
     * @return void
     */
    public function waitNextScraping(int $seconds): void
    {
        sleep($seconds);
    }

    /**
     * Method to get actual date
     *
     * @return string|false
     */
    public function getActualDate(): string
    {
        return date('d/m/Y H:i:s a', time());
    }
}
