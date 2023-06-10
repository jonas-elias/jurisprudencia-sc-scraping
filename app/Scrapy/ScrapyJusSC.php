<?php

namespace App\Scrapy;

use App\Scrapy\Trait\FormatScrapy;
use GuzzleHttp\Client;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use voku\helper\HtmlDomParser;

use function Hyperf\Coroutine\co;

/**
 * class ScrapyJusSC
 *
 * @author <jonas-elias/>
 */
class ScrapyJusSC
{
    use FormatScrapy;

    #[Inject]
    protected Client $clientHttp;

    /**
     * @var string $linkResultadosIniciais
     */
    private string $linkResultadosIniciais = 'https://busca.tjsc.jus.br/jurisprudencia/buscaajax.do?&categoria=acordaos&categoria=despachos&categoria=acma&categoria=decmonos&categoria=recurso&categoria=dmtrs&ps=50&pg=';

    /**
     * @var string $linkHtmlProcesso
     */
    private string $linkHtmlProcesso = 'https://busca.tjsc.jus.br/jurisprudencia/';

    /**
     * @var array $iconesResultadoInicial
     */
    private array $iconesResultadoInicial = [
        'rtf' => 0,
        'html' => 1,
        'lupa' => 2
    ];

    public function __construct()
    {
    }

    /**
     * Method to scrapy busca tjus sc
     *
     * @param array $pags
     * @return void
     */
    public function scrapyJusSC(array $pags): void
    {
        $textos = [];

        foreach ($pags as $pag) {
            $response = $this->clientHttp->get($this->linkResultadosIniciais . $pag);
            $htmlString = (string) $response->getBody();
            $divPrincipal = HtmlDomParser::str_get_html($htmlString)->find('#coluna_principal');

            foreach ($divPrincipal->find(".resultados") as $chaveResultado => $resultado) {

                foreach ($resultado->getElementByClass("icones") as $chaveIcone => $subValue) {
                    if ($chaveIcone === $this->iconesResultadoInicial['html']) {
                        $divElement = $subValue->find('.icones', 0);
                        $href = $divElement->find('a', 0)->href;
                        $response = $this->clientHttp->get($this->linkHtmlProcesso . $href);
                        $htmlString = (string) $response->getBody();
                        $htmlDomParser = HtmlDomParser::str_get_html($htmlString);
                        $colunaPrincipal = $htmlDomParser->find("#coluna_principal");
                        $resultados = $colunaPrincipal->find(".resultados");
                        $paragrafo = $this->formatParagrafo(($htmlDomParser->find('.integra_paragrafo'))->plaintext[0]);
                        $textos[$pag][$chaveResultado]['texto'] = $paragrafo;

                        foreach ($resultados->find('strong') as $cabecalho) {
                            $nextNode = $cabecalho->next_sibling();

                            if (
                                $cabecalho->plaintext == "Processo:" || $cabecalho->plaintext == "Relator:" ||
                                $cabecalho->plaintext == "Origem:" || $cabecalho->plaintext == "Orgão Julgador:" ||
                                $cabecalho->plaintext == "Classe:" || $cabecalho->plaintext == "Julgado em:"
                            ) {

                                $chaveCabecalho = $this->formatChave($cabecalho->plaintext);
                                $node = $this->formatNode($nextNode->data);

                                $textos[$pag][$chaveResultado][$chaveCabecalho] = $node;
                            }
                        }
                    }
                }
            }
        }
        try {
            foreach ($pags as $key => $value) {
                Db::table('jurisdicao')->insert($textos[$value]);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage() . $th->getLine());
        }
    }
}
