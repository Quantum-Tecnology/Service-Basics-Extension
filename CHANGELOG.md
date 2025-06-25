# CHANGE LOG

## V2.5.0 (23/06/2025)

# PT-BR

- Ajustado alguns codigos mortos no filter scope trait. @GustavoSantarosa

# EN

- Refactor filter scope method invocation logic. @GustavoSantarosa

## V2.5.0 (23/06/2025)

# PT-BR

- Incluido validação de case sensitive no search. @GustavoSantarosa

# EN

- Included case-sensitive validation in search. @GustavoSantarosa

## V2.4.15 (31/05/2025)

# PT-BR

- Correção de upload de arquivos, com problema em update e delete. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/17

# EN

- File upload fix, with problem on update and delete. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/17

## V2.4.14 (26/05/2025)

# PT-BR

- Verifique se o método existe antes de sincronizar dados relacionados na loja e atualizar serviços. @bhcosta90

# EN

- Ensure method exists before syncing related data in store and update services. @bhcosta90

## V2.4.13 (20/05/2025)

# PT-BR

- Ajustado uma limitação temporaria no update, pois ainda nao permite utilizar o each diretamente. @GustavoSantarosa

# EN

- Adjusted a temporary limitation in update, as it still does not allow using each directly. @GustavoSantarosa

## V2.4.12 (01/05/2025)

# PT-BR

- Passado somente o $id dentro do show no update. @GustavoSantarosa

# EN

- Only the $id is passed inside the show in the update. @GustavoSantarosa

## V2.4.11 (01/05/2025)

# PT-BR

- Agora é passado o $id de update para updating. @GustavoSantarosa
- Corrigido um use dentro de update. @GustavoSantarosa

# EN

- Now the $id of update is passed to updating. @GustavoSantarosa
- Fixed a use inside update. @GustavoSantarosa

## V2.4.10 (29/04/2025)

# PT-BR

- Criado config para parametros padroes no repositorio. @GustavoSantarosa
- Quando cria ou atualiza um determinado registro, realiza-se o show novamente para buscar os dados corretamente. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/15

# EN

- Created config for default parameters in the repository. @GustavoSantarosa
- When you create or update a particular record, the show is performed again to fetch the data correctly. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/15

## V2.4.9 (25/04/2025)

# PT-BR

- Melhorado a forma como o file é deletado na trait, facilitando e diminuindo e otimizando o form data recebido. @GustavoSantarosa

# EN

- Improved the way the file is deleted in the trait, making it easier and optimizing the received form data. @GustavoSantarosa

## V2.4.8 (24/04/2025)

# PT-BR

- Para filtrar os relacionamentos, esta dando erro que a variavel não existe. @bhcosta90

# EN

- To filter the relationships, it is giving an error that the variable does not exist. @bhcosta90

## V2.4.7 (24/04/2025)

# PT-BR

- Removido uma criação de atributo de `allowedIncludes` de archive model que nao é mais utilizado. @GustavoSantarosa

# EN

- Removed the creation of the `allowedIncludes` attribute from the archive model as it is no longer used. @GustavoSantarosa

## V2.4.6 (24/04/2025)

# PT-BR

- Alterado a variavel que era armazenada que ja estava depreciada para a query pura. @GustavoSantarosa

# EN

- Changed the variable that was stored, which was already deprecated, to the pure query. @GustavoSantarosa

## V2.4.5 (24/04/2025)

# PT-BR

- Descontinuado uma function `setQueryCustom`, por ser redudante, utilizar a `defaultQuery`. @GustavoSantarosa

# EN

- Discontinued the `setQueryCustom` function as it is redundant; use `defaultQuery` instead. @GustavoSantarosa

## V2.4.4 (23/04/2025)

# PT-BR

- No laravel 12, não usamos mais a palavra scope no começo para podermos filtrar. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/12/files

# EN

- In laravel 12, we no longer use the word scope at the beginning so we can filter. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/12/files

## V2.4.3 (22/04/2025)

# PT-BR

- Corrigido um problema na trait de scope, filtros de relações eram executados na model principal. @GustavoSantarosa

# EN

- Fixed a problem with Scope Trait, relationship filters were executed in the main model. @GustavoSantarosa

## V2.4.2 (17/04/2025)

# PT-BR

- Corrigindo um problema de que tenta utilizar um metodo de uma trait que não existe em todas as models. @bhcosta90

# EN

- Fixing an issue where you try to use a method from a trait that doesn't exist in all models @bhcosta90

## V2.4.1 (12/04/2025)

# PT-BR

- Corrigido um problema que estava ocorrendo na trait de files e storeService. @GustavoSantarosa

# EN

- Fixed an issue that was occurring in the files trait and storeService. @GustavoSantarosa

## V2.4.0 (11/04/2025)

# PT-BR

- Corrigido um problema que estava ocorrendo na trait de files e storeService. @GustavoSantarosa

# EN

- Fixed an issue that was occurring in the files trait and storeService. @GustavoSantarosa

## V2.4.0 (11/04/2025)

# PT-BR

- Implementado metodos de pre e pós nas seguintes traits store, update, destroy e restore. @GustavoSantarosa @bhcosta90.

# EN

- Implemented pre and post methods in the following traits: store, update, destroy, and restore. @GustavoSantarosa @bhcosta90.

## V2.3.3 (11/04/2025)

# PT-BR

- Verificar se o model em questão é do base model, se for ele utiliza a parte de arquivos. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/10.

# EN

- Check if the model in question is from the base model; if so, it uses the file-related functionality. @bhcosta90 in https://github.com/Quantum-Tecnology/Service-Basics-Extension/pull/10.

## V2.3.2 (10/04/2025)

# PT-BR

- Ao setar um sortBy, o sortBy setado nao era respeitado. @GustavoSantarosa

# EN

- When setting a sortBy, the set sortBy was not respected. @GustavoSantarosa

## V2.3.1 (10/04/2025)

# PT-BR

- Quando não informava o search, sempre pesquisava pelo like independente se haveria ou não. @bhcosta90

# EN

- When I didn't inform the search, I always searched for the like regardless of whether there would be one or not. @bhcosta90

## V2.3.0 (06/04/2025)

# PT-BR

- Implementado o retorno por Data. @GustavoSantarosa

# EN

- Implemented return by Date. @GustavoSantarosa

## V2.2.2 (05/04/2025)

# PT-BR

- Corrigido um problema em filter trait. @GustavoSantarosa
- Corrigido um problema em bootService trait. @GustavoSantarosa
- Composer update. @GustavoSantarosa

# EN

- Fixed an issue in the filter trait. @GustavoSantarosa
- Fixed an issue in the bootService trait. @GustavoSantarosa
- Composer update. @GustavoSantarosa

## V2.2.1 (30/03/2025)

# PT-BR

- Corrigido um problema no filetrait e limpado alguns codigos mortos. @GustavoSantarosa

# EN

- Fixed an issue in the filetrait and cleaned up some dead code. @GustavoSantarosa

## V2.2.0 (22/03/2025)

# PT-BR

- Criado a model de archives e sua trait de relacionamento para models @GustavoSantarosa
- Criado a trait para salvar arquivos na tabela archives @GustavoSantarosa
- Ajustado o store e update para se adequar as novas traits e automatizado o file @GustavoSantarosa
- Atualizado a vendor @GustavoSantarosa

# EN

- Created the archives model and its relationship trait for models @GustavoSantarosa
- Created the trait to save files in the archives table @GustavoSantarosa
- Adjusted the store and update to fit the new traits and automated the file @GustavoSantarosa
- Updated the vendor @GustavoSantarosa

## V2.1.1 (18/03/2025)

# PT-BR

- Corrigido um problema que estava ocorrendo na nova trait de filterInclude @GustavoSantarosa

# EN

- Fixed an issue that was occurring in the new filterInclude trait @GustavoSantarosa

## V2.1.0 (16/03/2025)

# PT-BR

- Implementado o phpCSFixer no pacote @GustavoSantarosa
- Renomeado a Trait filter include para se enquadrar ao padrao @GustavoSantarosa
- Abstraido as funções do baseService para traits para seguir o padrao do pacote controller @GustavoSantarosa
- Abstraido as funções do index para traits para facilitar a utilização @GustavoSantarosa
- Criado a trait de scopes para fazer as buscas automaticas de acordo com o que o usuario requisitar @GustavoSantarosa
  - Ideia inicial @bhcosta90
- Criado a trait de Search para poder organizar melhor as logicas dele @GustavoSantarosa
- Criado a trait de Sort para poder organizar melhor as logicas dele @GustavoSantarosa
- Criado a Trash de Sort para poder organizar melhor as logicas dele @GustavoSantarosa
- Ajustado o index e o show com as novas traits @GustavoSantarosa

# EN

- Implemented phpCSFixer in the package @GustavoSantarosa
- Renamed the Trait filter include to conform to the default @GustavoSantarosa
- Abstracted the functions of the baseservice for traits to follow the controller package pattern @GustavoSantarosa
- Abstracted the functions of the index for traits to facilitate usage @GustavoSantarosa
- Created the scopes trait to perform automatic searches according to user requests @GustavoSantarosa
  - Initial idea @bhcosta90
- Created the Search trait to better organize its logic @GustavoSantarosa
- Created the Sort trait to better organize its logic @GustavoSantarosa
- Created the Trash trait to better organize its logic @GustavoSantarosa
- Adjusted the index and show with the new traits @GustavoSantarosa
