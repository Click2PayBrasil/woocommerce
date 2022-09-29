# Plugin Click2pay para WooCommerce

Receba pagamentos via Pix, Cartão de crédito e boleto

<img src="https://click2pay.com.br/site/views/_assets/images/logo-white.svg" width="100" alt="Click2Pay Logo" />

- **Tags:** Pagamentos, Pix, Cartão de Crédito, Boleto, Click2pay
- **Version:** 1.0.5
- **WC requires at least:** 4.0.0
- **WC tested up to:** 6.9.2
- **License:** GNU General Public License v3.0

A solução completa para você receber seus pagamentos online.

## Descrição ##
A [Click2Pay](https://click2pay.com.br/) é uma fintech que atua como gateway de pagamento nas modalidades pix, cartão de crédito, boleto bancário e gestora de contas de  pagamento. Este plugin permite que os proprietários de lojas WooCommerce usem a Click2pay como um método de pagamento em sua loja.

Este Plugin atualmente oferece:

- Geração de PIX Dinâmico no pedido.
- Geração de Boleto Bancário.
- Pagamento via cartão de crédito.

### Requisitos do sistema: ###
* Versão do WooCommerce 4.0.0 até 6.9.2

##### Utilização do plugin Brazillian Market, para utilização de campos como CPF e/ou Data de Aniversário. 
    ⚠️ O CPF é obrigatório nas transações, já o campo de data de aniversário é obrigatório apenas para os clientes que utilizam 
    o serviço de anti fraude em transações com cartões de crédito.

Link do Plugin: 
[Brazilian Market on WooCommerce](https://br.wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/) 

`⚠️IMPORTANTE`
> Os requisitos do sistema foram definidos de acordo com os nossos testes. 
> 
> Se seu sistema não se encaixa nos requisitos, não significa que o módulo não vai funcionar em seu WordPress (WooCommerce), mas sim, que não testamos no mesmo ambiente.
> 
> **Portanto, não garantimos o funcionamento deste módulo em ambientes diferentes dos citados acima.**

# Instalando o plugin:

Antes de instalar o plugin, certifique-se que tenha instalado em sua página WordPress o plugin da loja virtual WooCommerce. Caso não possua, [faça o download] (https://br.wordpress.org/plugins/woocommerce/) e instale em seu site.

Para instalar o plugin, efetue o download dos arquivos do plugin e envie por FTP para seu servidor.

**1.** O primeiro passo é realizar o download dos arquivos da última versão do plugin em formato **zip** **"CODE > Download Zip"** no repositório.

**2.** Depois de baixar os arquivos, acesse a interface administrativa da loja (/wp-admin) e vá em **Plugins > Adicionar novo > Fazer o upload do plugin**, enviando o arquivo **click2pay-for-woocommerce.zip**

**3.** Caso prefira, os arquivos podem ser descompactados e enviados diretamente via FTP para a pasta de plugins da instalação do WordPress (dentro de **/wp-content/plugins**)

**4.** Após a instalação, clique em **Ativar o Plugin**. 
  
# Configurações do plugin Click2Pay para WooCommerce:
  
Antes de começar a receber pagamentos com a Click2Pay, o lojista deverá realizar a configuração do plugin, com suas credenciais e preferências.

**1.** Para iniciar a configuração do plugin, no painel administrativo da loja, acesse: **WooCommerce > Configurações > Pagamentos**

**2.** Na tela apresentada será listado os três tipos de pagamentos disponíveis:

  * Click2Pay - Cartão de crédito
  * Click2Pay - Pix
  * Click2Pay - Boleto

**3.** Ao lado direito de cada um dos tipos de pagamento há um botão denominado Gerenciar, que deverá ser clicado para que o lojista possa realizar a configuração do método de pagamento, inserir suas credenciais e preferências.

### PIX - Configuração, credenciais e preferênciais ###


![PIX](https://user-images.githubusercontent.com/109624050/182668316-b143389d-1a97-4a91-a65b-2dac515563c5.png)

**1.** Habilite o recebimento via Pix, na opção **Ativar método**.

**2.** Na opção **Título**, altere o texto que será exibido para os clientes da loja.

**3.** Na opção **Descrição**, altere o texto que descreve o método de pagamento.

**4.** Na opção **Validado Pix, em minutos**, altere o tempo de validade do QR Code Dinâmico e seu Copie & Cola.

**5.** Na opção **Client ID**, preencha com a credencial fornecida pela Click2Pay.

**6.** Na opção **Client Secret**, preencha com a credencial fornecida pela Click2Pay.

**7**. Na opção **Log de depuração**, habilite os logs caso precise analisar as requisições e retornos do método de pagamento.

`❗️ATENÇÃO`
>Importante salientar que **os códigos do tipo QR Code Dinâmico / Copie & Cola gerados em sandbox não são válidos.**
>
>Para simular a baixa de transações Pix em ambiente de teste (sandbox), utilize o **[simulador de pagamentos Pix](https://apisandbox.click2pay.com.br/c2p/)** em seu smartphone.

### Cartão de Crédito - Configuração, credenciais e preferênciais ###


![Cartão de crédito](https://user-images.githubusercontent.com/109624050/182669307-b2651f35-dddc-41be-89d6-05c2203e2254.png)


**1.** Habilite o recebimento via Cartão de Crédito, na opção **Ativar método.**

**2.** Na opção **Título**, altere o texto que será exibido para os clientes da loja.

**3.** Na opção **Descrição**, altere o texto que descreve o método de pagamento.

**4.** Na opção **Descrição na fatura**, preencha com o texto que irá acompanhar a fatura.

**5.** Na opção **Número de parcelas**, selecione o número máximo de parcelas disponíveis na loja.

**6.** Na opção **Parcela mínima**, preencha com o valor mínimo para cada parcela.

**7.** Na opção **Taxa de juros**, preencha com a taxa de juros que deve ser aplicada por parcela, nos casos em que o parcelamento terá a aplicação de juros.

**8.** Na opção **Parcelas sem juros**, selecione o número máximo de parcelas sem juros, que estará disponível na loja.

**9.** Na opção **Client ID**, preencha com a credencial fornecida pela Click2Pay.

**10**. Na opção **Client Secret**, preencha com a credencial fornecida pela Click2Pay.

**11**. Na opção **Chave Pública**, preencha com a credencial fornecida pela Click2Pay.

**12**. Na opção **Log de depuração**, habilite os logs caso precise analisar as requisições e retornos do método de pagamento.

`📘COMPRAS COM 1 CLIQUE`
>O plugin possui o recurso de salvar o cartão para compras futuras, sem necessidade do cliente digitar os dados do cartão de crédito novamente.

#### Realizando testes com cartões de crédito em ambiente de teste (sandbox) ####

Os seguintes números de cartão de crédito podem ser usados para simular transações em ambiente de teste (sandbox), para pagamentos bem-sucedidos:

| Número | CVV | Validade | Bandeira |
| :--- | :--- | :--- | :--- | 
| 4539003370725497   | 123            | 01/2030       | Visa     |
| 5356066320271893   | 123            | 01/2030       | Master   |
| 3765 902450 61698  | 123            | 01/2030       | Amex     |

Além disso, esses são os números "mágicos" de cartões que gerarão respostas específicas, úteis para testar diferentes cenários:

| Número |	Retorno |
| :--- | :--- |
| 6011457819940087 | A transação será recusada com um código "card_declined". |
| 4710426743216178 | A transação será recusada com um código "service_request_timeout". |


### Boleto Bancário - Configuração, credenciais e preferênciais ###

![b93c2a6-boleto](https://user-images.githubusercontent.com/109624050/182674448-55d6b001-b45e-4989-b7f5-61eff36263ea.png)

**1.** Habilite o recebimento via Boleto Bancário, na opção **Ativar método**.

**2.** Na opção **Título**, altere o texto que será exibido para os clientes da loja.

**3.** Na opção **Descrição**, altere o texto que descreve o método de pagamento.

**4.** Na opção **Dias até o vencimento**, preencha com o número de dias para vencimento do boleto.

**5.** Na opção **URL do logotipo**, preencha com a URL do logotipo que será exibido junto ao boleto. Frisando que os formatos aceitos são **png, svg, jpg e gif** com altura máxima de **100 pixels** e largura máxima de **200 pixels**.

**6.** Nas opções **Instruções - Linha 1 e Instruções - Linha 2**, preencha com informações para o pagamento que serão exibidas no boleto.

**7.** Na opção **Client ID**, preencha com a credencial fornecida pela Click2Pay.

**8.** Na opção **Client Secret**, preencha com a credencial fornecida pela Click2Pay.

**9.** Na opção **Log de depuração**, habilite os logs caso precise analisar as requisições e retornos do método de pagamento.

`❗️ATENÇÃO`
>Importante salientar que **os boletos gerados em sandbox não são válidos e não podem ser pagos** e possuem uma marca d'água ao fundo informando ser um boleto de teste.

# Funcionamento do plugin Click2Pay para WooCommerce 

Após o plugin ser habilitados nas modalidades de pagamento desejadas, já estará disponível na loja as opções de pagamentos no formato transparente. Desta forma, o cliente preenche todos os dados de pagamento na tela de **Finalizar Compra**, conforme imagens ilustrativas abaixo:

### Cartão de Crédito ###


![146eb0d-cartao](https://user-images.githubusercontent.com/109624050/182675536-455baabb-ea0e-4b54-9159-064cc7bd78d9.png)


### Pix ###


![4285d19-pix-01](https://user-images.githubusercontent.com/109624050/182675572-d85dd830-d76c-4a8c-ac6f-bb23af3f1c30.png)


![b894789-pix-02](https://user-images.githubusercontent.com/109624050/182675584-2a4d0aff-7229-4672-8f51-75a927e1311b.png)


### Boleto Bancário ###


![bc92378-boleto-01](https://user-images.githubusercontent.com/109624050/182675668-9afc7095-b3f9-4660-b0dd-a4257139077f.png)


![dfc7a79-boleto-02](https://user-images.githubusercontent.com/109624050/182675705-abbe22b4-3481-42c9-8fcc-69f7c8b4359b.png)



Desta forma, ao clicar em Finalizar Compra,, os dados do cliente serão validados e, caso esteja tudo correto, a cobrança será gerada e a compra finalizada. O cliente então é redirecionado para a página de compra finalizada.

