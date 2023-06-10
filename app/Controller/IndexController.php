<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use GuzzleHttp\Client;
use Hyperf\DbConnection\Db;
use voku\helper\HtmlDomParser;

class IndexController extends AbstractController
{
    public function index()
    {
        $pags = [15, 16, 17, 18];

        $textos = [];

        foreach ($pags as $pag) {
            $httpClient = new Client();
            $response = $httpClient->get('https://busca.tjsc.jus.br/jurisprudencia/buscaajax.do?&categoria=acordaos&categoria=despachos&categoria=acma&categoria=decmonos&categoria=recurso&categoria=dmtrs&ps=50&pg=' . $pag);
            $htmlString = (string) $response->getBody();

            $htmlDomParser = HtmlDomParser::str_get_html($htmlString);
            $element = $htmlDomParser->find("#coluna_principal");
            $element = $element->find(".resultados");

            foreach ($element as $key => $value) {
                foreach ($value->getElementByClass("icones") as $subkey => $subValue) {
                    if ($subkey === 1) {
                        $divElement = $subValue->find('.icones', 0);
                        $href = $divElement->find('a', 0)->href;
                        $response = $httpClient->get('https://busca.tjsc.jus.br/jurisprudencia/' . $href);
                        $htmlString = (string) $response->getBody();
                        $htmlDomParser = HtmlDomParser::str_get_html($htmlString);
                        $html = $htmlDomParser->find("#coluna_principal");
                        $html = $html->find(".resultados");
                        $html2 = $htmlDomParser->find('.integra_paragrafo');
                        $string = trim(preg_replace('/\s+/', ' ', mb_convert_encoding($html2->plaintext[0], 'utf-8')));
                        $textos[$pag][$key]['texto'] = $string;
                        foreach ($html->find('strong') as $strong) {
                            $nextNode = $strong->next_sibling();

                            if (
                                $strong->plaintext == "Processo:" || $strong->plaintext == "Relator:" ||
                                $strong->plaintext == "Origem:" || $strong->plaintext == "Orgão Julgador:" ||
                                $strong->plaintext == "Classe:" || $strong->plaintext == "Julgado em:"
                            ) {
                                $map = array(
                                    'á' => 'a',
                                    'é' => 'e',
                                    'í' => 'i',
                                    'ó' => 'o',
                                    'ú' => 'u',
                                    'ã' => 'a',
                                    'õ' => 'o',
                                    '~' => '',
                                );
                            $chave = strtr($strong->plaintext, $map);

                            $string = str_replace(array("\r", "\n", "\t"), '', trim($nextNode->data));
                            $textos[$pag][$key][strtolower(str_replace(" ", "_", str_replace(':', '', $chave)))] = $string;
                            }
                        }
                    }
                }
            }
        }

        try {
            Db::table('jurisdicao')->insert($textos[15]);
            Db::table('jurisdicao')->insert($textos[16]);
            Db::table('jurisdicao')->insert($textos[17]);
            Db::table('jurisdicao')->insert($textos[18]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
