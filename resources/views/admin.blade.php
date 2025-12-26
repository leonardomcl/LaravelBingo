<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Noel - Bingo Natal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,700|inter:400,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/js/app.js'])
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --christmas-red: #d42426;
            --christmas-green: #165b33;
            --gold: #f8b229;
            --snow: #f8f9fa;
        }

        body {
            background: linear-gradient(135deg, #165b33 0%, #0a2e1a 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #333;
        }

        .admin-container {
            max-width: 900px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 4px solid var(--gold);
            backdrop-filter: blur(10px);
        }

        .header-title {
            font-family: 'Instrument Sans', sans-serif;
            font-weight: 700;
            color: var(--christmas-red);
            text-shadow: 1px 1px 0px #fff;
        }

        #big-ball {
            width: 180px;
            height: 180px;
            font-size: 5rem;
            color: var(--christmas-red);
            border: 8px solid var(--christmas-red);
            background: radial-gradient(circle at 30% 30%, #fff, #eee);
            transition: all 0.3s ease;
        }

        .drawn-badge {
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--christmas-red);
            color: white;
            font-weight: bold;
            margin: 5px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease;
        }

        .btn-draw {
            background-color: var(--christmas-green);
            border: none;
            padding: 15px 40px;
            font-size: 1.25rem;
            font-weight: bold;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .btn-draw:hover {
            background-color: #1a7a43;
            transform: scale(1.05);
        }

        .winner-alert {
            background-color: var(--gold);
            border-left: 5px solid #b8860b;
            color: #000;
            font-weight: bold;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="p-3 p-md-5">

    <div class="container admin-container p-4 p-md-5 mx-auto">
        <div class="text-center mb-4">
            <h1 class="header-title"><i class="fas fa-hat-santa"></i> Painel do Papai Noel</h1>
            <p class="text-muted">Controle Global do Bingo de Natal</p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-md-6 text-center border-end">
                <div id="big-ball"
                    class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow mb-4">
                    ?
                </div>
                <button id="btn-draw" class="btn btn-draw text-white w-100 mb-3" onclick="playBingoBall()">
                    <i class="fas fa-dice"></i> SORTEAR BOLA
                </button>

                <form action="{{ route('bingo.reset') }}" method="POST"
                    onsubmit="return confirm('ATENÇÃO: Isso apagará todas as bolas sorteadas. Continuar?')">
                    @csrf
                    <button type="submit" class="btn btn-light w-100  text-muted btn-sm">Reiniciar Jogo <i class="fa-solid fa-arrows-rotate"></i></button>
                </form>
            </div>

            <div class="col-md-6">

                <div class="mt-1 p-3 border border-success rounded bg-light">
                    <h5 class="fw-bold text-success"><i class="fas fa-list-ol"></i> Quem está por pouco:</h5>
                    <ul id="leaderboard" class="list-group list-group-flush">
                        <li class="list-group-item text-muted small">Aguardando sorteio...</li>
                    </ul>
                </div>

                <h5 class="fw-bold mb-3 mt-3"><i class="fas fa-history"></i> Últimas Bolas</h5>
                <div id="drawn-history" class="d-flex flex-wrap justify-content-start">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="winnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">🚨 BINGO DETECTADO!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <h2 id="winnerName" class="text-danger fw-bold"></h2>
                    <p>Reclamou o prêmio agora mesmo!</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {

            function initBingoListeners() {
                if (typeof window.Echo !== 'undefined') {
                    console.log("Echo conectado! Ouvindo eventos...");

                    const channel = window.Echo.channel('bingo-channel');

                    // Ouvinte de Vencedores
                    channel.listen('PlayerWon', (e) => { // Ponto antes do nome
                        $('#winnerName').text(e.playerName);
                        var myModal = new bootstrap.Modal(document.getElementById('winnerModal'));
                        myModal.show();
                    });


                    // Ouvinte de Ranking
                    channel.listen('.RankingUpdated', (data) => { // Ponto antes do nome
                        console.log("Ranking recebido via Reverb:", data.ranking);

                        let list = $('#leaderboard');
                        list.empty();

                        if (!data.ranking || data.ranking.length === 0) {
                            list.append(
                                '<li class="list-group-item text-muted">Aguardando sorteio...</li>');
                            return;
                        }

                        data.ranking.forEach((player, index) => {
                            let badgeClass = 'bg-success';
                            let itemClass = '';

                            if (player.missing <= 2) {
                                badgeClass =
                                    'bg-danger animate__animated animate__flash animate__infinite';
                                itemClass = 'list-group-item-warning fw-bold';
                            }

                            list.append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center ${itemClass}">
                        <div>
                            <span class="badge bg-secondary me-2">${index + 1}º</span>
                            ${player.name}
                        </div>
                        <span class="badge ${badgeClass} rounded-pill">Faltam ${player.missing}</span>
                    </li>
                `);
                        });
                    });


                } else {
                    console.log("Echo não carregado ainda, tentando novamente...");
                    // Se o Echo ainda não carregou, tenta novamente em 500ms
                    setTimeout(initBingoListeners, 500);
                }
            }

            initBingoListeners();
        });

        const somUrl = "{{ asset('build/assets/small-ball-393217.mp3') }}";

        function playBingoBall() {
            const audio = new Audio(somUrl);
            audio.play().catch(e => console.log("Erro ao tocar áudio: ", e));
        }

        $('#btn-draw').click(function() {
            let btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> SORTEANDO...');

            $.post("{{ route('bingo.draw') }}", {
                    _token: "{{ csrf_token() }}"
                })
                .done(function(data) {
                    $('#big-ball').text(data.number).hide().fadeIn(300);
                    $('#drawn-history').prepend(`<div class="drawn-badge">${data.number}</div>`);
                    btn.prop('disabled', false).html('<i class="fas fa-dice"></i> SORTEAR BOLA');
                })
                .fail(function(err) {
                    alert(err.responseJSON.message || "Erro ao sortear");
                    btn.prop('disabled', false).html('<i class="fas fa-dice"></i> SORTEAR BOLA');
                });
        });
    </script>
</body>

</html>
