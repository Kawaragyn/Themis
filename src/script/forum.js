const topicosDoBanco = [
  {
    id: 1,
    nome: "Tomioka",
    usuario: "@antifelicidade",
    data: "06/09/2026",
    conteudo: "Já parou para pensar em como o clima político e as incertezas do país afetam diretamente a nossa saúde mental? A exposição constante a notícias difíceis, a polarização nas redes e a sensação de falta de perspectiva em relação ao futuro geram um desgaste silencioso. Muitas vezes, a sobrecarga de informações e a ansiedade sobre os rumos da sociedade se somam a quadros de exaustão e depressão. Cuidar de si e estabelecer limites no consumo de notícias também é uma forma de preservação. Afinal, como construir um debate público saudável se as pessoas estão mentalmente esgotadas?",
    curtidas: 0,
    comentarios: 0,
    fotoPerfil: "../../image/pessoa1.jpeg"
  },
  {
    id: 2,
    nome: "Sanemi",
    usuario: "@ventosopra",
    data: "06/09/2026",
    conteudo: "Que raiva dessa palhaçada de todo mundo achar que entende de política agora só porque lê manchete de rede social! Passam o dia inteiro reclamando do país, apontando o dedo pra governante, mas na hora de cobrar leis de verdade ou estudar como o sistema funciona, somem. Ficar com raivinha na internet e não mover um dedo na vida real pra mudar as coisas é fácil demais. Se quer criticar a situação do país, primeiro toma vergonha na cara, entende os seus deveres e vai pra cima cobrar quem manda!",
    curtidas: 0,
    comentarios: 0,
    fotoPerfil: "../../image/pessoa2.jpeg"
  }
];

function carregarTopicos() {
    const container = document.getElementById('lista-topicos');
    if (!container) return;

    container.innerHTML = '';

    topicosDoBanco.forEach(topico => {
        const cardHTML = `
            <article class="topic-card" data-id="${topico.id}">
                <div class="topic-header">
                    <div class="user-info">
                        <img src="${topico.fotoPerfil}" alt="Foto de ${topico.nome}" class="avatar">
                        <h3 class="name">${topico.nome}</h3>
                        <span class="user">${topico.usuario}</span>
                    </div>
                </div>
                <p class="topic-preview">${topico.conteudo}</p>
                <div class="topic-interacoes">
                    <div class="curtidas">
                        <button class="btn-curtir">
                            <i data-lucide="heart"></i>
                        </button>
                        <span class="num-curtidas">${topico.curtidas}</span>
                    </div>
                    <div class="comentarios">
                        <button class="btn-comentar">
                            <i data-lucide="message-circle"></i>
                        </button>
                        <span class="num-comentario">${topico.comentarios}</span>
                    </div>
                    <span class="date">${topico.data}</span>
                </div>        
            </article>
        `;
        container.innerHTML += cardHTML;
    });

    if (window.lucide) {
        lucide.createIcons();
    }

    adicionarEventosCurtida();
}

function adicionarEventosCurtida() {
    const botoesCurtir = document.querySelectorAll('.btn-curtir');

    botoesCurtir.forEach(botao => {
        botao.addEventListener('click', () => {
            const containerCurtidas = botao.parentElement;
            const contadorSpan = containerCurtidas.querySelector('.num-curtidas');
            let valorAtual = parseInt(contadorSpan.textContent, 10);

            if (botao.classList.contains('curtido')) {
                botao.classList.remove('curtido');
                contadorSpan.textContent = valorAtual - 1;
            } else {
                botao.classList.add('curtido');
                contadorSpan.textContent = valorAtual + 1;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', carregarTopicos);