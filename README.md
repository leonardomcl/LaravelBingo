# 🎄 Bingo do Papai Noel - Real-time Laravel 🎅

Um sistema de Bingo completo, desenvolvido para eventos de confraternização, focado em alta performance e interatividade em tempo real. O projeto utiliza **Laravel 11**, **Reverb** (WebSockets) e **Redis** para proporcionar uma experiência fluida tanto para o Administrador quanto para os Jogadores.




https://github.com/user-attachments/assets/e5dfd379-5911-4b12-bab0-c2ff76bcbbdf




## 🚀 Funcionalidades

- **Painel do Papai Noel (Admin):**
  - Sorteio de bolas com validação de duplicidade.
  - Ranking em tempo real (Top 5) de quem está mais próximo do Bingo.
  - Reset global de jogo com regeneração automática de cartelas.
- **Área do Jogador:**
  - Cartelas geradas dinamicamente com números ordenados por coluna (padrão profissional B-I-N-G-O).
  - Marcação automática de números sorteados com efeitos visuais e sonoros.
  - Efeito de confetes ao completar a cartela.
- **Tecnologia Real-time:**
  - WebSockets via **Laravel Reverb** para atualização instantânea sem F5.
  - Cache de ranking via **Redis** para processamento ultra-rápido.

## 🛠️ Tech Stack

- **Framework:** [Laravel 11](https://laravel.com)
- **Real-time:** [Laravel Reverb](https://reverb.laravel.com)
- **Frontend:** Blade, Tailwind/Bootstrap, jQuery, Canvas-confetti
- **Banco de Dados:** MySQL & Redis
- **Segurança:** Middlewares de proteção por Role (Admin/Player)

## 📦 Instalação

1. Clone o repositório:
2. Instale as dependências ( composer install && npm install )
3. Configure o arquivo .env e após execute ( npm run build )
4. Execute as migrações ( php artisan migrate )
5. Inicie os serviços ( php artisan reverb:start )
