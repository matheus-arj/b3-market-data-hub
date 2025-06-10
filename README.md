# 🚀 B3 Market Data Hub  
**Hub de dados para cadastro de instrumentos financeiros da B3, integrando API, processamento em Python e armazenamento MongoDB.**
Este projeto foi desenvolvido como parte de um processo seletivo para a empresa **OliveiraTrust**. Ele demonstra habilidades em integração de APIs, processamento de dados e utilização de tecnologias modernas.

## 📌 Visão Geral  
O **B3 Market Data Hub** é uma solução que centraliza e disponibiliza dados financeiros da B3, permitindo a extração e organização de informações sobre os instrumentos negociados na bolsa.<br>
O sistema facilita a análise e processamento dos arquivos de Cadastro de Instrumentos, que devem ser baixados manualmente do site da B3 e enviados para extração. A partir desses dados, ele proporciona uma base confiável para análises financeiras, desenvolvimento de estratégias de investimento e integração com sistemas de trading.

## ⚙️ Funcionalidade do Projeto  
- **API de Ingestão (Laravel)** → Upload seguro de arquivos `.csv` e `.xlsx`.  
- **Processamento de Dados (Python Worker)** → Monitoramento e extração de dados essenciais.  
- **Armazenamento Centralizado (MongoDB)** → Dados organizados e prontos para consulta.  

---

## 🚀 Tecnologias utilizadas  
- **PHP 8 + Laravel**  
- **Python 3.10+**  
- **MongoDB**  
- **Docker + Docker Compose**  
- **Watchdog (Python)**  
- **Pandas (Python)**  

---

## 📁 Estrutura do Projeto  
```sh
.
├── laravel/ # Projeto Laravel (upload e API de histórico)
├── python/
│   └── worker/
│       └── main.py # Script principal do worker
├── docker/ # Dockerfiles e configs
├── docker-compose.yml
└── README.md
```

## 🔧 Como executar o projeto

### 1️⃣ Clone o repositório e acesse a pasta do projeto:
```Bash
git clone https://github.com/matheus-arj/b3-market-data-hub.git
cd b3-market-data-hub
```

### 2️⃣ Configure as variáveis de ambiente:
```Bash
cp laravel/.env.example laravel/.env
```

### 3️⃣ Suba os containers
**🔹 Necessário Docker e Docker Compose instalados.**

```sh
docker-compose up --build
```
Este comando irá subir: ✅ Laravel (PHP) ✅ MongoDB ✅ Worker Python ✅ Nginx

### 4️⃣ Executar o Worker manualmente

```sh
python3 -m venv venv
source venv/bin/activate  # Linux/macOS
venv\Scripts\activate     # Windows

pip install -r python/worker/requirements
python python/worker/main.py
```

## 📝 Endpoints da API Laravel
### 🚀 Upload de arquivos
API responsável pelo upload de arquivos de **Cadastro de Instrumentos da B3**.
```Http
POST /api/upload
Content-Type: multipart/form-data
```

| Campo | Tipo       | Obrigatório |
| ----- | ---------- | ----------- |
| file  | .csv/.xlsx | ✅ Sim       |

Exemplo de cURL:
```sh
curl -X POST http://localhost:8080/api/upload \
  -F 'file=@/caminho/do/seu/arquivo.csv'
```
#### **⚠️ O Worker Python precisa estar em execução para processar os arquivos após o upload.**

## 🔍 History API
Retorna os dados dos arquivos enviados, permitindo consulta por nome do arquivo e data de referência.
```Http
GET /api/history
```
#### Parâmetros opcionais: original_name, reference_date
Exemplo de resposta:
```Json
[
  {
    "original_name": "InstrumentsConsolidatedFile_20250509_1.csv",
    "stored_name": "1747110609_InstrumentsConsolidatedFile_20250509_1.csv",
    "hash": "0933269574ee3700c7d11de814869ce8",
    "reference_date": "2025-05-09",
    "updated_at": "2025-05-13T04:30:09.864000Z",
    "created_at": "2025-05-13T04:30:09.864000Z",
    "id": "6822cad123e3705f810bddb2"
  }
]
```

## 🔍 Search API
Consulta e retorna dados extraídos do documento Cadastro de Instrumentos da B3, incluindo ISIN, ticker, nome da empresa e categoria do ativo.
```Http
GET /api/search
```
Exemplo de resposta:
```Json
    {
        "RptDt": "2025-06-06",
        "TckrSymb": "0FEA11",
        "MktNm": "EQUITY-CASH",
        "SctyCtgyNm": "FUNDS",
        "ISIN": "BR0FEACTF006",
        "CrpnNm": "SPIM FUNDO DE INVESTIMENTO IMOBILIÁRIO",
        "id": "6847a533b4e2f9f98ec0fdec"
    },
```

## 🧠 Sobre o Worker Python
### O Worker é responsável por: 
✅ Monitorar a pasta laravel/public/uploads <br>
✅ Detectar e processar arquivos `.csv` e `.xlsx`<br>
✅ Extrair os principais campos:
`RptDt`
`TckrSymb`
`MktNm`
`SctyCtgyNm`
`ISIN`
`CrpnNm`<br>
✅ Inserir os dados na coleção Record do MongoDB

## 💡 Arquivos para Teste
Você pode encontrar arquivos de cadastro na URL abaixo, contendo aproximadamente 75.000 linhas: 🔗 [Dados públicos da B3](https://www.b3.com.br/pt_br/market-data-e-indices/servicos-de-dados/market-data/consultas/boletim-diario/dados-publicos-de-produtos-listados-e-de-balcao/)

Para baixar: 1️⃣ Clique em uma data 2️⃣ Clique em "Cadastro de Instrumentos (Listado)" 3️⃣ Baixe o arquivo