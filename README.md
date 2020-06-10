![tests](https://github.com/jeyroik/extas-jsonrpc/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-jsonrpc/coverage.svg?branch=master)
<a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat" alt="PHPStan Enabled"></a> 
<a href="https://codeclimate.com/github/jeyroik/extas-jsonrpc/maintainability"><img src="https://api.codeclimate.com/v1/badges/5981e38afb0c2f62c78f/maintainability" /></a>
<a href="https://github.com/jeyroik/extas-installer/" title="Extas Installer v3"><img alt="Extas Installer v3" src="https://img.shields.io/badge/installer-v3-green"></a>
[![Latest Stable Version](https://poser.pugx.org/jeyroik/extas-jsonrpc/v)](//packagist.org/packages/jeyroik/extas-jsonrpc)
[![Total Downloads](https://poser.pugx.org/jeyroik/extas-jsonrpc/downloads)](//packagist.org/packages/jeyroik/extas-jsonrpc)
[![Dependents](https://poser.pugx.org/jeyroik/extas-jsonrpc/dependents)](//packagist.org/packages/jeyroik/extas-jsonrpc)

# Описание

Extas-совместимый JSON RPC сервер.

# install operations

## install jsonrpc plugin and default operation

`# vendor/bin/extas i`

# usage out of the box

## start server

`# php -S 0.0.0.0:8080 -t src/public`

## make request

`# curl -X POST localhost:8080/api/jsonrpc -d '{"id": "request id", "method":"operation.index"}'`

# spec generation

This package allows generating specs upon to `InstallSection-`plugins, extended from `extas\components\plugins\intsall\InstallSection`.

This package allows generating specs upon to `InitSection-`plugins, extended from `extas\components\plugins\init\InitSection`.

`*` - you can reset this prefix (see below).

There is extas-command for spec generation. Command is ready-to-extending, so you can add your own options, using `extas-commands-options`-notation. See `extas.json` of the current package for examples.

## install command

- `# vendor/bin/extas i`
- `# vendor/bin/extas list` - command `jsonrpc` should be listed.

## generate specs

`# vendor/bin/extas jsonrpc --export-path generated.extas.json`

This will generate extas-compatible configuration in ready-to-install format. 

So you can install specs by

`# vendor/bin/extas i`

## --export-path

Define path to store generated specs.
- Default: `CWD/specs.extas.json`
- You can pass relative and absolute path.

`CWD` - Current Working Directory.

## --prefix-jsonrpc-install

Allow setting prefix for plugins searching by install section crawler.

- Default: `Install`

## --path-jsonrpc-install

Allow setting path for searching plugins by install section crawler.

- Default: current working directory.

## --prefix-jsonrpc-doc-comment

Allow setting prefix for classes searching by doc-comment crawler.

- Default: `Install`

## --path-jsonrpc-doc-comment

Allow setting path for searching classes by doc comment crawler.

- Default: current working directory.

## -f --filter

Allow filtering operations names.

- Default: ` `
- Example: `# vendor/bin/extas jsonrpc -f workflow` will generate specs only for operations with `workflow` in a name.

## -e --only-edge

Sometimes you want to make entity name shorter and use only last word of plugin name. 

You can do this with the option `-e`:

For example, we have plugin name `workflow schema`.

- Default: `0` - generate entity name `workflow.schema`.
- With edging: `# vendor/bin/extas jsonrpc -e 1` will produce entity name `schema`.

# specs examples

You can find them here:
 
- `resources/create.spec.json`
- `resources/index.spec.json`
- `resources/update.spec.json`
- `resources/delete.spec.json`

# injection into json-rpc process

Current package provide next stages to allow you to inject into the json-rpc process:
- `before.run.jsonrpc` - before every json-rpc processing.
- `before.run.jsonrpc.<method.name>`
- `after.run.jsonrpc.<method.name>`
- `after.run.jsonrpc` - after every json-rpc processing. 

# Generators

Package use `extas-generators` package for getting generators.

You should turn generators on if you want to use one.

You can find ready-to-config default generators configuration in `resources/generators.json`. Just copy-paste them into your `extas.json`.

# Crawlers

Package use `extas-crawlers` package for getting crawlers.

You should turn crawlers on if you want to use one.

You can find ready-to-config default crawlers configuration in `resources/crawlers.json`. Just copy-paste them into your `extas.json`.