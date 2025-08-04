# Processo de criação e desenvolvimento do teste

Escolhi para seguir pela **#Tarefa 2 Trocando a cor dos botões**, pois foi a que mais
me interessei, principalmente por ter as tags Front e Back, indicando que seria necessário
trabalhar com as duas frentes. 

O início do Desenvolvimento, foi arquitetando como seria feito a funcionalidade principal.
Então foquei em desenvolver primeiro o Comando CLI, que faz a alteração das cores.

Iniciei criando o command, para executar a tarefa e setando os elementos de botões
no código mesmo, hardcoded. Após fazer o comando funcionar como o esperado, passei
a complementar o módulo com outras funcionalidades, como a possibilidade de 
alterar as cores pelo painel administrativo do Magento, pensando na facilidade
de uso para o cliente.

Neste momento, complementei o escopo do módulo, adicionando as configurações no 
painel adminstrativo, criando um Color Picker para permitir ao usuário escolher a 
cor desejada e disponibilizando um campo textarea para o cliente inserir as 
tags as quais deseja aplicar a cor definida. 

Após implementar a alteração de cores via CLI e pelo painel administrativo,
eu foquei em organizar o código e deixar o mais limpo e organizado possível, sempre
visando Cleand Code e boas práticas no desenvolvimento em Magento 2.

Também implementei testes unitários pensando na qualidade do código.

Foi um desafio muito interessante e enriquecedor, pois consegui implementar o que
o Teste requeria e ainda complementar a funcionalidade pensando na experiência
do cliente, que ao meu ver, é sempre importante nós desenvolvedores termos em 
vista.

___
# Módulo Hibrido ColorChanger

## Funcionalidades
O módulo **Hibrido_ColorChanger** permite a personalização de cores de 
elementos HTML, como botões, diretamente pelo painel administrativo do 
Magento ou via linha de comando, tornando a customização visual do seu 
site mais flexível e dinâmica.

## Caminhos para Ativação do Módulo
1. Certifique-se de que o módulo está instalado no diretório `app/code/Hibrido/ColorChanger`.
2. Execute o comando para habilitar o módulo:
   ```bash
   bin/magento module:enable Hibrido_ColorChanger
   ```
3. Atualize o registro do módulo e o cache:
   ```bash
   bin/magento setup:upgrade
   bin/magento cache:flush
   ```

## Troca de Cores

### Pelo Painel Administrativo
1. Acesse o painel administrativo do Magento.
2. Navegue até **Stores > Configuration > Hibrido > ColorChanger**.
3. Escolha a cor desejada para os botões ou outras tags configuradas.
4. Salve as alterações para aplicar a nova cor no frontend.

### Via Linha de Comando
Você também pode alterar a cor de um elemento via CLI, utilizando o comando personalizado do módulo:

```bash
bin/magento color:change hex store_id
```
- Substitua `hex` pelo código hexadecimal da cor desejada.
- Substitua `store_id` pelo Id da store na qual deseja modificar as cores.

## Resultado das mudanças de cores nos botões:

### Alteração no frontend da loja:
- [![Visualização da Alteração](https://ibb.co/FLLT8XgH)](https://ibb.co/FLLT8XgH)

### Resposta do comando CLI:
- [![Comando CLI](https://ibb.co/JWjVgwMZ)](https://ibb.co/JWjVgwMZ)

