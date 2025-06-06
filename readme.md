# RCN\_ButtonColor

Módulo Magento 2 para personalização da cor de botões via comando CLI, com suporte por **store view**.

## Objetivo

Permitir que o cliente personalize a cor dos botões primários da loja diretamente pelo comando de CLI: bin/magento color:change <hex> <store_id>, ou via painel admin de forma independente para cada store view.

---

## Funcionalidades

* Comando CLI para atualizar a cor diretamente via terminal.
* Limpeza automática dos caches após atualização via CLI.
* Configuração de cor via Admin:
  `Loja > Configurações > Configuração > RCN > Button Color`
* Opção para ativar/desativar o módulo por store view.
* Injeção de CSS inline automático com base na cor configurada.

---

## Estrutura

* **Bloco**: `InjectCss` gera o estilo CSS inline com a cor definida.
* **Plugin**: `InjectCssPlugin` controla a renderização do bloco com base na configuração de ativo/desativado do módulo.
* **Command**: `bin/magento color:change <hex> <store_id>` altera a cor via terminal e limpa os caches.
* **Configuração Admin**: adicionada ao XML `system.xml` com escopo por store.

---

## Como usar

### 1. Instalação

Copie o módulo para `app/code/RCN/ButtonColor` e execute:

```bash
php bin/magento setup:upgrade
php bin/magento cache:flush
```

### 2. Instalação via Composer (GitHub)

Se preferir, é possível instalar o módulo diretamente via Composer apontando para este repositório:
Adicione o repositório ao composer.json do seu projeto Magento:

```bash
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/renannedochetko/rcn-buttoncolor"
  }
]
```

Execute o comando de instalação:

```bash
composer require rcn/buttoncolor
```

Após a instalação, finalize com:

```bash
php bin/magento setup:upgrade
php bin/magento cache:flush
```

### 3. Configuração via Admin

Acesse:
`Admin > Stores > Configuration > RCN > Button Color`

* **Enable Module**: ativa/desativa a funcionalidade do módulo (Inicialmente desativado por padrão).
* **Button Color**: define a cor no formato `#ff0000`,`#fff`,`000`,`000000`.
* **Importante:**  
> As configurações do módulo foram desenvolvidas exclusivamente para o escopo de _store view_.  
> Para que as alterações surtam efeito, é necessário selecionar uma loja específica (store view) no seletor de escopo do Magento antes de aplicar as configurações.  
> O módulo **não aplica configurações salvas em escopos mais amplos** como `Default Config` ou `Website`.

### 4. Atualização via CLI

```bash
php bin/magento color:change ff5733 1
```

* `ff5733`: código da cor (sem `#`)
* `1`: ID da visualização de loja (Store View)

A cor é salva e os caches necessários são limpos automaticamente.

---

## Escopo de cache e performance

* O bloco `InjectCss` usa cache key baseada na store view para garantir isolamento por escopo.
* A configuração é salva com `ScopeInterface::SCOPE_STORES`, respeitando o contexto multi-store.

---

## Requisitos

* Magento 2.4.x+
* PHP 7.4+

---

## Conclusão

Este módulo foi desenvolvido com foco em flexibilidade, boas práticas e controle de escopo no Magento 2. Ele serve como base sólida para personalizações visuais baseadas em configuração, respeitando a arquitetura do framework e mantendo compatibilidade com múltiplas stores.

---

## Testes e desenvolvimento

Durante o desenvolvimento:

* Todas as alterações via CLI foram testadas com variações de input (`3 ou 6 caracteres hex`, com ou sem `#`).
* O impacto do cache foi analisado e controlado com `getCacheKeyInfo()` e `$_isScopePrivate`.

---

## Contato

Desenvolvido por RCN para fins de avaliação técnica e reuso em projetos Magento 2.
