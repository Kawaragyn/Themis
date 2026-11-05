const form = document.getElementById('form-postagem');
const editor = document.getElementById('editor-texto');
const inputConteudo = document.getElementById('input-conteudo');
const inputImagem = document.getElementById('input-imagem');

inputImagem.addEventListener('change', function(event) {
    const arquivo = event.target.files[0]; // Pega o arquivo de imagem
    
    if (arquivo) {
        const leitor = new FileReader();
        
        leitor.onload = function(e) {
            // 1. Cria a tag da imagem
            const img = document.createElement('img');
            img.src = e.target.result;
            
            // 2. Estilos para permitir o redimensionamento manual pelo usuário
            img.style.width = '200px'; // Tamanho inicial padrão
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            img.style.display = 'block';
            img.style.margin = '10px 0';
            img.style.borderRadius = '8px';
            
            // ⚡ O segredo para habilitar o redimensionamento arrastando o mouse:
            img.style.resize = 'both'; 
            img.style.overflow = 'auto';

            // 3. Garante que o foco está no editor
            editor.focus();

            // 4. Insere a imagem exatamente na posição do cursor usando Selection API
            if (window.getSelection) {
                const selecao = window.getSelection();
                
                if (selecao.rangeCount > 0) {
                    const range = selecao.getRangeAt(0);
                    
                    // Verifica se o cursor está realmente dentro do editor antes de inserir
                    if (editor.contains(range.commonAncestorContainer)) {
                        range.deleteContents(); // Apaga o texto se o usuário tiver selecionado algo
                        range.insertNode(img);  // Insere a imagem no local correto
                        
                        // Move o cursor para logo após a imagem inserida
                        range.setStartAfter(img);
                        range.setEndAfter(img);
                        selecao.removeAllRanges();
                        selecao.addRange(range);
                    } else {
                        // Se o cursor não estava no editor, joga a imagem no final do texto
                        editor.appendChild(img);
                    }
                }
            }
        }
        
        leitor.readAsDataURL(arquivo);
    }
    
    // Limpa o campo para permitir enviar a mesma imagem em seguida, se quiser
    inputImagem.value = '';
});

// Passa todo o HTML (texto + imagens redimensionadas) para o PHP salvar
form.addEventListener('submit', function(e) {
    inputConteudo.value = editor.innerHTML; 
});
