![tests](https://github.com/jeyroik/extas-jsonrpc/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-jsonrpc/coverage.svg?branch=master)
<a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat" alt="PHPStan Enabled"></a> 
<a href="https://codeclimate.com/github/jeyroik/extas-jsonrpc/maintainability"><img src="https://api.codeclimate.com/v1/badges/5981e38afb0c2f62c78f/maintainability" /></a>

# Описание

Extas-совместимый JSON RPC сервер.

# install operations

## copy default classes container

```
# cp vendor/jeyroik/extas-foundation/resources/container.dist.php src/configs/container.php
# cp vendor/jeyroik/extas-foundation/resources/container.dist.json src/configs/container.json
```

## install jsonrpc plugin and default operation

`# vendor/bin/extas i`

# usage out of the box

## start server

`# php -S 0.0.0.0:8080 -t src/public`

## make request

`# curl -X POST localhost:8080/api/jsonrpc -d '{"id": "request id", "method":"operation.index"}'`

# spec generation

This package allows generating specs upon to `PluginInstall-`plugins, extended from `extas\components\plugins\PluginInstallDefault`.

`*` - you can reset this prefix (see below).

There is extas-command for spec generation.

## install command

- `# vendor/bin/extas i`
- `# vendor/bin/extas list` - command `jsonrpc` should be listed.

## generate specs

`# vendor/bin/extas jsonrpc -s generated.extas.json`

This will generate extas-compatible configuration in ready-to-install format. 

So you can install specs by

`# vendor/bin/extas i`

## -s --specs

Define path to store generated specs.
- Default: `CWD/specs.extas.json`
- You can pass relative and absolute path.

`CWD` - Current Working Directory.

## -p --prefix

Allow to set prefix for plugins searching.

- Default: `PluginInstall`

## -f --filter

Allow to filter plugins and install just some of them.

- Default: `''`
- Example: `# vendor/bin/extas jsonrpc -f workflow` will generate specs only for plugins with `workflow` in a name.

## -e --only-edge

Entity name is borrowed from a `PluginInstallDefault::getPluginName(): string`.

Sometimes you want to make entity name shorter and use only last word of plugin name. 

You can do this with the option `-e`:

For example, we have plugin name `workflow schema`.

- Default: `0` - generate entity name `workflow.schema`.
- Example: `# vendor/bin/extas jsonrpc -e 1` will produce entity name `schema`.

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