# Como rodar o ambiente Docker para desenvolvimento

Configuração pronta para desenvolvimento com:

-   PHP-FPM (container `app`)
-   Nginx (container `web`)
-   Postgres (container `db`)
-   Node/Vite para hot-reload (container `node`)

Passos rápidos:

1. Copie o .env (se ainda não existir):

    cp .env.example .env

2. Build e subir os containers:

```bash
docker compose up -d --build
```

3. Instalar dependências e preparar a aplicação (uma vez ou quando atualizar dependências):

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec node npm install
```

4. Hot-reload (Vite):

-   Por padrão o container `node` já executa `npm run dev` e expõe a porta 5173. O Nginx está configurado para proxiar os requests \_vite para o servidor Node durante desenvolvimento.
-   Se preferir rodar o Vite localmente, execute `npm run dev` fora do container.

5. Acesse a aplicação em http://localhost

Notas:

-   Variáveis de conexão com o banco estão definidas no `docker-compose.yml` (DB_HOST=db, DB_PORT=5432). Ajuste conforme necessário.
-   Em macOS, para performance, o volume usa montagem direta; se enfrentar lentidão, considere usar Mutagen ou docker-sync.
