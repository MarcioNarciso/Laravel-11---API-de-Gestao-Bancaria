# API de Gestão Bancária

O desafio consistia em criar dois endpoints: "/conta" e "/transacao".

O endpoint "/conta" deveria criar e fornecer informações sobre o número da conta
e o saldo. O endpoint "/transacao" seria responsável por realizar diversas operações
financeiras.

Os endpoints deveriam seguir os padrões de entrada e saída no formato JSON 
conforme descrito a seguir.

<b>POST /transacao</b>

INPUT (JSON)
```
{"forma_pagamento":"D", "conta_id": 1234, "valor":10}
```

<br>
OUTPUT (JSON)

Em caso de sucesso:
```
HTTP STATUS 201
{“conta_id”: 1234, “saldo”: 189.70}
```

Caso não tenha saldo disponível:
```
HTTP STATUS 404
```

<br>
<b>POST /conta</b>

INPUT (JSON)
```
{ "conta_id": 1234, "valor":10}
```

<br>
OUTPUT (JSON)

Em caso de sucesso:
```
HTTP STATUS 201
{“conta_id”: 1234, “saldo”: 189.70}
```

<br>
<b>GET /conta?id=1234</b>

OUTPUT (JSON)

Em caso de sucesso:
```
HTTP STATUS 200
{“conta_id”: 1234, “saldo”: 200}
```

Caso a conta não exista:
```
HTTP STATUS 404
```

