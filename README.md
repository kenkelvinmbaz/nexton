# nexton
Uma API Rest que oferece um meio de pagamento entre usuários e lojistas.

INSTRUÇÃO
---------------

1. Para rodar a API, tenha certeza que você tem o composer está instalado na sua máquina(notebook). 
2. Clone o projeto.
3. abre um terminal do seu notebook. navega até a pasta do projeto dentro do terminal. Uma fez feito, roda o seguinte comando : composer install para instalar algumas dependencia do Laravel.
4. Para rodar o servidor da aplicção, basta você rodar esse comando dentro do seu terminal: php artisan serve. 
o servidor local vai ser lançado com  http://127.0.0.1:8000. Mas você pode tbm user esse servidor https://nexton.nuvenspay.com/ (se quiser receber email e sms).
5. rodar o comando: php artisan migrate   para replicar as tabelas criadas no banco de dados.

NB: LEMBRANDO QUE O PROJETO USA A CONEXÃO COM O BANCO DE DADOS MYSQL.

END-POINT
---------------

Aqui está uma lista dos end-point que você precisa para executar algumas tarefea:
A. signin: é o responsável pela criação de registro para usuarios comum. Para poder o usar você precisa enviar a requisição via POST com os seguintes objetos:

POST : http://127.0.0.1:8000/api/signin

{
    "name":"Paulo",
    "last_name":"De Oliveira",
    "phone_number":"41996014577",
    "email":"paulodeoliveira@nexton.com",
    "pin":"0000",
    "password":"12345678",
    "cpf":"061.741.98.012"
}

B. merchant/signin: é o responsável pela criação de registro para lojistas. Para poder o usar você precisa enviar a requisição via POST com os seguintes objetos:

POST : http://127.0.0.1:8000/api/merchant/signin

{
    "name":"Loja SAMSUNG",
    "last_name":"SAMSUNG SA",
    "phone_number":"41992643665",
    "email":"lojanextom@nexton.com",
    "pin":"0000",
    "password":"12345678",
    "cpf":"45.788.459/0001-30"
}

C. merchant/login: é o responsável pela conexão de lojistas no sistema. Para poder o usar você precisa enviar a requisição via POST com os seguintes objetos:

POST : http://127.0.0.1:8000/api/merchant/login

{
    "email":"lojanexton@nexton.com",
    "password":"12345678"
}


D. merchant/login: é o responsável pela conexão de usuarios comuns no sistema. Para poder o usar você precisa enviar a requisição via POST com os seguintes objetos:

POST : http://127.0.0.1:8000/api/login

{
    "email":"paulodeoliveira@nexton.com",
    "password":"12345678"
}

E. transfer: é o responsável pela efetuar transferência de dinheiro. Para poder o usar você precisa enviar a requisição via POST com os seguintes objetos:

POST : http://127.0.0.1:8000/api/transfer

{
    "amount_send": "0.5", 
    "email_receiver":"leculte01@gmail.com",
    "currency": "2"
}

Você precisa informar a moeda. 1 para Dolar 2 para Real

F. balance: é o responsável pela leitura do saldo de usuarios. Para poder o usar você precisa enviar a requisição via GET com os seguintes objetos:

GET : http://127.0.0.1:8000/api/balance

NB: Para que esse operação seja verificada, o usuario é autenticado pelo Token que deve ser passado via header (Token bearer)










