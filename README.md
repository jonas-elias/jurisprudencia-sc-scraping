# 📚 Scraping de Jurisprudências - TJSC

Este repositório tem como objetivo aplicar o scraping no website [https://busca.tjsc.jus.br/jurisprudencia](https://busca.tjsc.jus.br/jurisprudencia). O código foi desenvolvido em PHP e é capaz de extrair informações relevantes, como número do processo, relator, origem, órgão julgador, entre outros, e persisti-las no banco de dados PostgreSQL.

## Utilização

1. Execute a imagem Docker através do seguinte comando:
```
docker-compose up -d
```

2. Dentro do container Hyperf, execute os comandos para instalar o componente e atualizar as migrações:
```
php bin/hyperf.php migrate:install
php bin/hyperf.php migrate:fresh
```

3. Realize a geração das jurisprudências utilizando o comando:
```
php bin/hyperf.php scrapy:scjus numeroDePaginas
```

Após a execução bem-sucedida desses passos, as informações extraídas estarão disponíveis no banco de dados PostgreSQL. 

## Observação
Em caso de erros ou bloqueios durante o processo, os logs estarão disponíveis em `runtime/logs/` para análise posterior.
