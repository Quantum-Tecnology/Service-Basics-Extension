# CHANGE LOG

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
