# Configuração M-Pesa

## Credenciais Necessárias

Para integrar com a API do M-Pesa, você precisa obter as seguintes credenciais junto à Vodacom:

### 1. Ambiente Sandbox (Para Testes)
```
MPESA_PUBLIC_KEY=sua_chave_publica_sandbox
MPESA_API_HOST=https://api.sandbox.vm.co.mz
MPESA_API_KEY=sua_api_key_sandbox
MPESA_ORIGIN=developer.mpesa.vm.co.mz
MPESA_SERVICE_PROVIDER_CODE=seu_codigo_provedor_sandbox
MPESA_INITIATOR_IDENTIFIER=seu_iniciador_sandbox
MPESA_SECURITY_CREDENTIAL=sua_credencial_seguranca_sandbox
```

### 2. Ambiente Produção (Para Uso Real)
```
MPESA_PUBLIC_KEY=sua_chave_publica_producao
MPESA_API_HOST=https://api.vm.co.mz
MPESA_API_KEY=sua_api_key_producao
MPESA_ORIGIN=api.vm.co.mz
MPESA_SERVICE_PROVIDER_CODE=seu_codigo_provedor_producao
MPESA_INITIATOR_IDENTIFIER=seu_iniciador_producao
MPESA_SECURITY_CREDENTIAL=sua_credencial_seguranca_producao
```

## Como Obter as Credenciais

1. **Registre-se no Portal do Desenvolvedor M-Pesa**
   - Acesse: https://developer.mpesa.vm.co.mz
   - Crie uma conta de desenvolvedor

2. **Crie uma Aplicação**
   - No painel do desenvolvedor, crie uma nova aplicação
   - Escolha o tipo de aplicação apropriado (C2B - Customer to Business)

3. **Obtenha as Credenciais**
   - Public Key: Chave pública para autenticação
   - API Key: Chave da API para autorização
   - Service Provider Code: Código único do seu negócio
   - Initiator Identifier: Identificador do iniciador das transações
   - Security Credential: Credencial de segurança criptografada

## Configuração no Projeto

1. **Copie as credenciais para o arquivo .env**
   ```bash
   cp .env.example .env
   ```

2. **Edite o arquivo .env e adicione suas credenciais**
   - Substitua os valores vazios pelas suas credenciais reais

3. **Limpe o cache de configuração**
   ```bash
   php artisan config:clear
   ```

## Testando a Integração

### Códigos de Resposta M-Pesa

- **INS-0**: Transação bem-sucedida
- **INS-1**: Erro interno, volte a tentar novamente
- **INS-2**: API inválida, volte a tentar novamente
- **INS-4**: Usuário não ativo
- **INS-5**: Transação cancelada pelo usuário
- **INS-6**: Transação falhou
- **INS-9**: O tempo expirou. Volte a tentar
- **INS-10**: Transação duplicada
- **INS-16**: Erro interno, volte mais tarde
- **INS-2006**: Saldo insuficiente
- **INS-2051**: Número de telefone inválido

### Números de Teste (Sandbox)

Para testes no ambiente sandbox, use números especiais fornecidos pela Vodacom que simulam diferentes cenários.

## Suporte

Para mais informações:
- Documentação oficial: https://developer.mpesa.vm.co.mz
- Suporte técnico: suporte@vm.co.mz
