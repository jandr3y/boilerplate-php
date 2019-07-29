## DOC ADMIN

Este é um modulo do sistema para administração de dados e do sistema em geral, aqui você poderá alterar grande parte dos registros do seu banco de dados de forma genérica, e também criar novos modelos com o gerador. Este módulo depende muito da aplicação ainda, e não é um plano futuro fazer ele flexivel, por isso qualquer falha na estrutura do projeto base pode comprometer suas funcionalidades.

**ATENÇÃO** Esta area do sistema não é recomendada para utilizar em produção devido a grande acessibilidade de fazer **merda**


### Recomendações

- Se for utilizar este módulo em produção tente ao máximo definir regras de segurança para acessar esta parte do sistema, é possível restringir o acesso somente a um IP para logar no painel com HTACCESS em seu servidor Apache ou Nginx.


### Dependências

- Módulo FSW


### Desenvolvedor

Se você for desenvolver algo aqui:

- Não faça com que o sistema dependa do módulo Administrativo.
- Não desenvolva coisas especificas para certo modelo.
- Tudo deve ser generico e que possa ser reutilizado.
- Tudo que você achar que pode aplicar em uma camada de utilidades gerais/globais, não use aqui.
- Abstraia
