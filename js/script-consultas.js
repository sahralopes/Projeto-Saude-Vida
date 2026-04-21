document.addEventListener("DOMContentLoaded", () => {
    const lista = document.getElementById("lista-consultas");
  
    // Faz a requisição ao backend para pegar as consultas reais
    fetch('api-consultas.php')
      .then(response => response.json())
      .then(consultas => {
        
        if (!consultas || consultas.length === 0) {
          lista.innerHTML = `
            <div class="msg-nenhuma">
              Você ainda não possui consultas agendadas.
            </div>
          `;
          return;
        }
  
        // Limpa lista antes de adicionar
        lista.innerHTML = '';

        consultas.forEach(c => {
          const dataObj = new Date(c.horario);
          const dataFormatada = dataObj.toLocaleDateString('pt-BR');
          const horaFormatada = dataObj.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
  
          const div = document.createElement("div");
          div.className = "consulta-card";
  
          div.innerHTML = `
            <h2>${c.especialidade}</h2>
            <p><strong>Paciente:</strong> ${c.nome_paciente}</p>
            <p><strong>Data:</strong> ${dataFormatada} às ${horaFormatada}</p>
          `;
  
          lista.appendChild(div);
        });
      })
      .catch(error => {
        console.error('Erro ao buscar consultas:', error);
        lista.innerHTML = '<div class="msg-nenhuma">Erro ao carregar consultas.</div>';
      });
  });

  document.addEventListener("click", async function(e) {
    if (e.target.classList.contains("cancelar-btn")) {

        if (!confirm("Tem certeza que deseja cancelar esta consulta?")) {
            return;
        }

        const id = e.target.getAttribute("data-id");

        const dados = new FormData();
        dados.append("id", id);

        const resp = await fetch("cancelar-consulta.php", {
            method: "POST",
            body: dados
        });

        const json = await resp.json();

        if (json.status === "ok") {
            alert("Consulta cancelada!");
            location.reload();
        } else {
            alert("Erro: " + (json.msg || "Desconhecido"));
        }
    }
});

function cancelarConsulta(id) {
      if (!confirm("Tem certeza que deseja cancelar essa consulta?")) return;

      const formData = new FormData();
      formData.append("id_consulta", id);

      fetch("pag-consultas.php", {
            method: "POST",
            body: formData
            })
      .then(r => r.json())
      .then(res => {
      if (res.status === "ok") {
      document.querySelector(`tr[data-id='${id}']`).remove();

      if (document.querySelectorAll("#lista-consultas tr").length === 0) {
        location.reload();
        }

        } else {
          alert("Erro ao cancelar: " + res.msg);
          }
        })
        .catch(err => alert("Erro: " + err));
        }
