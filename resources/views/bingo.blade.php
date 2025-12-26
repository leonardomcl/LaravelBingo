<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bingo Noel - Bingo Natal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/js/app.js'])
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'monospace', monospace;
        }

        .card-start {
            width: 96%;
            max-width: 700px;
            margin-top: 4px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .slot {

            transition: all 0.3s;
            cursor: default;
            font-size: 0.98rem;
            font-weight: bold;
            background-color: #fffffff8;
            color: #000000;
            user-select: none;
            cursor: pointer;

        }

        .marked {
            background-color: #66e16ec7 !important;
            color: #000 !important;
            font-weight: bold;
        }

        .slot.just-marked {
            animation: lucky-hit 1s ease-out;
            z-index: 10;
        }

        @keyframes lucky-hit {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7);
                background-color: #fff;
            }

            30% {
                transform: scale(1.3);
                box-shadow: 0 0 20px 10px rgba(255, 215, 0, 0.5);
                background-color: #f8b229;
                /* Cor dourada natalina */
                color: white;
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 215, 0, 0);
            }
        }

        /* Animação suave para os badges de histórico */
        .badge-new {
            animation: slideInDown 0.5s bounce;
        }
    </style>
</head>

<body class="d-flex mb-5 vh-100">
    <div class="container py-5 mb-5 text-center">
        <h1 class="text-danger mb-4 fw-bold">Minha Cartela Natalícia 🎄</h1>

        <button onclick="claimBingo()" class="btn btn-success btn-lg w-75 mb-4 fw-bold">GRITAR BINGO <i
                class="fa-solid fa-trophy"></i></button>

        <div class="bingo-card shadow-lg bg-white p-3 border border-danger border-5 rounded mb-5">
            <div class="row g-0 bg-danger text-white fw-bold mb-2 rounded">
                <div class="col p-2 fs-4 fw-bold">B</div>
                <div class="col p-2 fs-4 fw-bold">I</div>
                <div class="col p-2 fs-4 fw-bold">N</div>
                <div class="col p-2 fs-4 fw-bold">G</div>
                <div class="col p-2 fs-4 fw-bold">O</div>
            </div>

            {{-- Renderização da Grade --}}
            <div class="mb-5">
                @foreach ($userCard->numbers as $row)
                    <div class="row g-0">
                        @foreach ($row as $number)
                            @php
                                // Verifica se o número já foi sorteado ou se é o centro (0 / FREE)
                                $isMarked = in_array($number, $drawnNumbers) || $number === 0;
                            @endphp

                            <div class="col border p-2 d-flex align-items-center justify-content-center slot {{ $isMarked ? 'marked' : '' }}"
                                data-number="{{ $number }}" style="width: 70px; height: 70px;">

                                @if ($number === 0)
                                    <span class="text-success"><i class="fas fa-gift"></i></span>
                                @else
                                    {{ $number }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="mt-4 mb-5">
                <h5 class='text-center border p-2 mb-4 fw-bold'>BOLAS JÁ SORTEADAS</h5>
                <div id="drawn-numbers-list" class="d-flex flex-wrap justify-content-center gap-2">
                    @foreach ($drawnNumbers as $n)
                        <div class="badge bg-danger rounded-circle p-2 fs-5"
                            style="max-width:54px; max-height:54px; width:54px; height:54px; display: flex;justify-content: center;align-items: center; font-weight: bold;">
                            {{ $n }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <br>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
        </script>

        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>


        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
            function claimBingo() {
                $.post("{{ route('bingo.claim') }}", {
                        _token: "{{ csrf_token() }}"
                    })
                    .done(() => alert("Papai Noel recebeu seu grito de BINGO!"));
            }



            $(document).ready(function() {
                // Escutando o canal do bingo


                const somUrl = "{{ asset('build/assets/tennis-ball-hit-386155.mp3') }}";

                function playMatchBall() {
                    const audio = new Audio(somUrl);
                    audio.currentTime = 0; // Reinicia o som se clicar várias vezes rápido
                    audio.play();
                }

                window.Echo.channel('bingo-channel')
                    .listen('BingoCommands', (e) => {
                        console.log(e);
                        const comando = e.command;

                        switch (comando) {
                            case 'resetar':
                                alert("🔄 O JOGO FOI REINICIADO PELO PAPAI NOEL! SUA CARTELA SERÁ ATUALIZADA.");
                                // Recarrega a página para atualizar a cartela
                                location.reload();
                                break;
                        }
                    });

                window.Echo.channel('bingo-channel')
                    .listen('BallDrawn', (e) => {
                        const drawnNumber = e.number;

                        // 1. Notificação visual rápida
                        console.log("Bola sorteada: " + drawnNumber);

                        // 2. Marcar na cartela se o jogador tiver o número
                        const slot = $(`.slot[data-number="${drawnNumber}"]`);
                        if (slot.length > 0) {
                            playMatchBall();
                            slot.addClass('marked just-marked');

                            const offset = slot.offset();
                            const width = slot.width();
                            const height = slot.height();

                            confetti({
                                particleCount: 20,
                                spread: 50,
                                origin: {
                                    x: (offset.left + width / 2) / window.innerWidth,
                                    y: (offset.top + height / 2) / window.innerHeight
                                },
                                colors: ['#ff0000', '#165b33', '#f8b229'] // Cores de Natal
                            });

                            // Remove a classe de animação após 1s para permitir que ela ocorra de novo se necessário
                            setTimeout(() => {
                                slot.removeClass('just-marked');
                            }, 1000);

                            checkWinner(); // Função para conferir se completou linha/coluna
                        }

                        // 3. Adicionar à lista de bolas já sorteadas na tela
                        // Atualiza a lista de bolas com animação
                        const newBadge = $(`
            <div class="badge bg-danger rounded-circle p-3 m-1 shadow-sm" style="width:54px; height:54px; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 1.2rem;">
                ${drawnNumber}
            </div>
        `).hide();

                        $('#drawn-numbers-list').prepend(newBadge);
                        newBadge.fadeIn(400);
                    });
            });

            function dispararConfetes() {
                var duration = 5 * 1000;
                var animationEnd = Date.now() + duration;
                var defaults = {
                    startVelocity: 30,
                    spread: 360,
                    ticks: 60,
                    zIndex: 0
                };

                function randomInRange(min, max) {
                    return Math.random() * (max - min) + min;
                }

                var interval = setInterval(function() {
                    var timeLeft = animationEnd - Date.now();

                    if (timeLeft <= 0) {
                        return clearInterval(interval);
                    }

                    var particleCount = 50 * (timeLeft / duration);
                    // Confetes saindo das laterais
                    confetti(Object.assign({}, defaults, {
                        particleCount,
                        origin: {
                            x: randomInRange(0.1, 0.3),
                            y: Math.random() - 0.2
                        }
                    }));
                    confetti(Object.assign({}, defaults, {
                        particleCount,
                        origin: {
                            x: randomInRange(0.7, 0.9),
                            y: Math.random() - 0.2
                        }
                    }));
                }, 500);
            }

            function checkWinner() {
                const allSlots = $('.slot');
                const markedSlots = $('.slot.marked');

                // 2. Se a quantidade de marcados for igual ao total de slots, temos um vencedor!
                if (allSlots.length > 0 && allSlots.length === markedSlots.length) {

                    // Evita que a função dispare múltiplos alertas se o usuário continuar recebendo bolas
                    if (!window.bingoDeclarado) {
                        window.bingoDeclarado = true;

                        // Efeito visual de celebração
                        dispararConfetes();

                        // Envia para o servidor para o Admin saber
                        claimBingo();
                    }
                }
            }
        </script>
</body>

</html>
