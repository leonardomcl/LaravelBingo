<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cards;
use App\Events\BallDrawn;
use App\Events\BingoCommands;
use App\Events\RankingUpdated;
use App\Models\DrawnNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BingoController extends Controller
{
    //
    public function auth(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Busca o usuário pelo nome ou cria um novo
        $user = User::firstOrCreate(
            ['name' => $request->name],
            ['role' => 'player']
        );

        // Realiza o login do usuário 
        Auth::login($user, false);

        // Gerar cartela se o utilizador for jogador e não tiver uma
        if ($user->role === 'player' && !$user->card()->exists()) {
            $this->generateBingoCard($user->id);
        }

        return ($user->role === 'admin')
            ? redirect()->route('bingo.admin')
            : redirect()->route('bingo.index');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function bingo()
    {
        // Pega apenas os números sorteados como um array simples
        $drawnNumbers = DrawnNumbers::pluck('number')->toArray();

        // Pega a cartela do usuário logado
        $userCard = auth()->user()->card;

        return view('bingo', [
            'drawnNumbers' => $drawnNumbers,
            'userCard' => $userCard
        ]);
    }
    public function admin()
    {
        return view('admin');
    }

    private function generateBingoCard($userId)
    {
        $ranges = [
            'B' => range(1, 15),
            'I' => range(16, 30),
            'N' => range(31, 45),
            'G' => range(46, 60),
            'O' => range(61, 75),
        ];

        $columns = [];
        foreach ($ranges as $letter => $numbers) {
            shuffle($numbers);
            // 1. Pega 5 números aleatórios
            $selected = array_slice($numbers, 0, 5);

            // 2. Ordena esses 5 números em ordem crescente
            sort($selected);

            $columns[$letter] = $selected;
        }

        $grid = [];
        for ($row = 0; $row < 5; $row++) {
            $grid[$row] = [
                $columns['B'][$row],
                $columns['I'][$row],
                $row === 2 ? 0 : $columns['N'][$row], // setar 0 como centro "FREE"
                $columns['G'][$row],
                $columns['O'][$row],
            ];
        }

        $card = Cards::firstOrNew(['user_id' => $userId]);

        // Atribui os búmeros
        $card->numbers = $grid;

        // Salva no banco de dados
        $card->save();
    }


    public function draw()
    {
        // 1. Pega números já sorteados
        $alreadyDrawn = DrawnNumbers::pluck('number')->toArray();

        if (count($alreadyDrawn) >= 75) {
            return response()->json(['message' => 'Todas as bolas já foram sorteadas!'], 400);
        }

        // 2. Sorteia um número único
        do {
            $number = rand(1, 75);
        } while (in_array($number, $alreadyDrawn));

        // 3. SALVA NO BANCO PRIMEIRO (Para o ranking ler o dado atualizado)
        DrawnNumbers::create(['number' => $number]);

        // Atualiza a lista local para o cálculo do ranking sem precisar consultar o banco de novo
        $allDrawnIncludingNew = array_merge($alreadyDrawn, [$number]);

        // 4. CALCULA O RANKING
        $players = \App\Models\User::where('role', 'player')->with('card')->get();
        $ranking = [];

        foreach ($players as $player) {
            $cardNumbers = collect($player->card->numbers)->flatten()->filter(fn($n) => $n > 0);

            // Comparamos com a lista que já inclui a bola nova
            $missingCount = $cardNumbers->diff($allDrawnIncludingNew)->count();

            $data = [
                'name' => $player->name,
                'missing' => $missingCount
            ];

            // Salva no Cache
            Cache::put("player_score_{$player->id}", $data, now()->addHours(2));
            $ranking[] = $data;
        }

        // 5. ORDENA E ENVIA O RANKING
        usort($ranking, function ($a, $b) {
            return $a['missing'] <=> $b['missing'];
        });

        $topRanking = array_slice($ranking, 0, 5);
        broadcast(new RankingUpdated($topRanking));

        // 6. DISPARA A BOLA PARA OS JOGADORES
        broadcast(new BallDrawn($number))->toOthers();

        return response()->json(['number' => $number]);
    }

    // Método para resetar o jogo
    public function resetGame()
    {
        // 1. Limpa os números sorteados da rodada anterior
        DrawnNumbers::truncate();

        // LIMPEZA SELETIVA (Não desloga ninguém)
        $keys = \Illuminate\Support\Facades\Redis::keys('*player_score_*');
        foreach ($keys as $key) {
            // Remove o prefixo automático do Laravel se necessário
            $cleanKey = str_replace(config('database.redis.options.prefix'), '', $key);
            \Illuminate\Support\Facades\Redis::del($cleanKey);
        }

        // 3. Pega todos os jogadores e gera novas cartelas
        $players = \App\Models\User::where('role', 'player')->get();

        foreach ($players as $player) {
            $this->generateBingoCard($player->id);
        }

        // 4. Avisa todo mundo que o jogo resetou (o JS vai dar location.reload())
        broadcast(new BingoCommands('resetar'));

        return back()->with('success', 'O Papai Noel reiniciou o jogo e entregou cartelas novas!');
    }

    public function claim(Request $request)
    {
        $user = auth()->user();

        // Dispara um evento para o Admin saber quem ganhou
        // Você pode criar um evento 'PlayerWon' similar ao 'BallDrawn'
        broadcast(new \App\Events\PlayerWon($user->name))->toOthers();

        return response()->json(['message' => 'Bingo enviado! Aguarde a conferência.']);
    }
}
