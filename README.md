# extas-jsonrpc

Extas JsonRPC package

# install operations

`# vendor/bin/extas i`

# usage out of the box

## start server

'# php -S 0.0.0.0:8080 -t src/public'

## make request

`# curl -X POST localhost:8080/api/jsonrpc -d '{"id": "request id", "method":"operation.index"}'`

# spec examples

You can find them here:
 
- `resources/create.spec.json`
- `resources/index.spec.json`
- `resources/update.spec.json`
- `resources/delete.spec.json`