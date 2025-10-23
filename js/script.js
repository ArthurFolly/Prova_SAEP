function validarLogin() {
    const u = document.getElementById('nome_usuario').value.trim();
    const s = document.getElementById('senha').value;
    if (!u || !s) { 
        alert('Preencha usuário e senha!'); 
        return false; 
    }
    return true;
}

function validarCadastroUsuario() {
    const usuario = document.getElementById('nome_usuario').value.trim();
    const senha = document.getElementById('senha').value;
    const confirmar = document.getElementById('confirmar_senha').value;

    if (usuario.length < 3) { 
        alert('Usuário deve ter pelo menos 3 caracteres!'); 
        return false; 
    }
    if (senha.length < 6) { 
        alert('Senha deve ter pelo menos 6 caracteres!'); 
        return false; 
    }
    if (senha !== confirmar) { 
        alert('As senhas não coincidem!'); 
        return false; 
    }
    return true;
}

function validarProduto() {
    const nome = document.getElementById('nome').value.trim();
    const min = document.getElementById('estoque_minimo').value;
    if (!nome) { 
        alert('Nome é obrigatório!'); 
        return false; 
    }
    if (!min || min <= 0) { 
        alert('Estoque mínimo deve ser maior que 0!'); 
        return false; 
    }
    return true;
}

function validarMovimentacao() {
    const prod = document.querySelector('select[name="produto_id"]').value;
    const tipo = document.querySelector('input[name="tipo"]:checked');
    const qtd = document.querySelector('input[name="quantidade"]').value;
    const data = document.querySelector('input[name="data"]').value;

    if (!prod) { alert('Selecione um produto!'); return false; }
    if (!tipo) { alert('Selecione entrada ou saída!'); return false; }
    if (!qtd || qtd <= 0) { alert('Quantidade deve ser maior que 0!'); return false; }
    if (!data) { alert('Selecione a data!'); return false; }
    return true;
}

function editarProduto(p) {
    document.getElementById('id').value = p.id;
    document.getElementById('nome').value = p.nome;
    document.getElementById('descricao').value = p.descricao || '';
    document.getElementById('material').value = p.material || '';
    document.getElementById('tamanho').value = p.tamanho || '';
    document.getElementById('peso').value = p.peso || '';
    document.getElementById('estoque_minimo').value = p.estoque_minimo;
    document.getElementById('btnEnviar').name = 'editar';
    document.getElementById('btnEnviar').textContent = 'Salvar Alterações';
}

// CORRIGIDO: toggleSenha com SVG completo e sem erros
function toggleSenha(campoId, elemento) {
    const campo = document.getElementById(campoId);
    const svg = elemento.querySelector('svg');
    
    if (campo.type === 'password') {
        campo.type = 'text';
        // OLHO FECHADO (senha visível)
        svg.innerHTML = `
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486z"/>
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299a3.5 3.5 0 0 0 4.474 4.474l-.823-.823a2.5 2.5 0 0 1-2.829-2.829z"/>
            <path d="M3.35 5.47a.5.5 0 0 0-.7-.7L1.5 6.172l-.75-.75a.5.5 0 1 0-.7.7l.75.75-.75.75a.5.5 0 0 0 .7.7l.75-.75.75.75a.5.5 0 0 0 .7-.7l-.75-.75.75-.75a.5.5 0 0 0-.7-.7z"/>
        `;
    } else {
        campo.type = 'password';
        // OLHO ABERTO (senha oculta)
        svg.innerHTML = `
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
        `;
    }
}

// RELÓGIO AUTOMÁTICO
function atualizarRelogio() {
    const agora = new Date();
    const opcoes = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    };
    document.getElementById('relogio').textContent = agora.toLocaleDateString('pt-BR', opcoes);
}

// Atualiza a cada segundo
setInterval(atualizarRelogio, 1000);
window.onload = atualizarRelogio;