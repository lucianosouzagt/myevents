# CI de Testes (GitHub Actions)

Este repositório possui um workflow chamado `ci-tests` que executa a suíte de testes do Laravel em cada push e pull request para `main/master`.

## O que ele faz
- Instala PHP 8.3 com extensões necessárias (sqlite, pdo_sqlite, mbstring, intl)
- Executa `composer install` com dependências de desenvolvimento
- Roda `php artisan test` usando o ambiente `testing`

## Como bloquear PRs que falham
1. No GitHub, acesse Settings → Branches → Branch protection rules
2. Adicione regra para a branch `main` (ou `master`)
3. Marque “Require status checks to pass before merging”
4. Selecione o check `ci-tests / php-tests` como obrigatório

Com isso, PRs só poderão ser mesclados se os testes passarem.

