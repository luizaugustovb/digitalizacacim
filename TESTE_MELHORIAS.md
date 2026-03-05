# 🧪 Teste das Melhorias de Importação

## Comandos para Testar

### 1. Verificar Convênios Atuais
```bash
php artisan tinker --execute="App\Models\Convenio::all(['id', 'nome', 'codigo'])"
```

### 2. Simular Importação (Teste Manual)

Crie um arquivo `test_importacao.php` na raiz:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Convenio;
use Illuminate\Support\Facades\Log;

// Simular cod_guia vindos do SQL Server
$codsGuia = [
    'UNIMED',           // Já existe
    'BRADESCO SAÚDE',   // Já existe (busca por similaridade)
    'GOLDEN CROSS',     // Novo - será criado
    'AMIL ONE',         // Novo - será criado
    'SUS',              // Novo - será criado
];

echo "=== TESTE DE CRIAÇÃO AUTOMÁTICA DE CONVÊNIOS ===\n\n";
echo "Convênios antes do teste: " . Convenio::count() . "\n\n";

foreach ($codsGuia as $codGuia) {
    echo "Processando: {$codGuia}\n";
    
    // Normalizar
    $nomeConvenio = trim(strtoupper($codGuia));
    $nomeConvenioLimpo = preg_replace('/[0-9\s\-\.]+/', '', $nomeConvenio);
    
    // Buscar
    $convenio = Convenio::whereRaw('UPPER(nome) = ?', [$nomeConvenio])
        ->orWhereRaw('UPPER(codigo) = ?', [$nomeConvenio])
        ->first();
    
    if (!$convenio && strlen($nomeConvenioLimpo) >= 3) {
        $convenio = Convenio::whereRaw('UPPER(nome) LIKE ?', ["%{$nomeConvenioLimpo}%"])
            ->first();
    }
    
    if ($convenio) {
        echo "  ✓ Encontrado: {$convenio->nome} (ID: {$convenio->id})\n";
    } else {
        $convenio = Convenio::create([
            'nome' => ucwords(strtolower($codGuia)),
            'codigo' => $nomeConvenio,
            'observacoes' => 'Convênio criado automaticamente durante teste',
            'ativo' => true,
        ]);
        echo "  ✓ CRIADO: {$convenio->nome} (ID: {$convenio->id})\n";
    }
    
    echo "\n";
}

echo "Convênios após o teste: " . Convenio::count() . "\n";
echo "\nLista completa:\n";
foreach (Convenio::orderBy('created_at', 'desc')->get() as $c) {
    echo "  - {$c->nome} (Código: {$c->codigo})\n";
}
```

Execute:
```bash
php test_importacao.php
```

### 3. Testar Estrutura de Pastas

Faça upload de um documento em um pedido que tenha convênio associado e verifique:

```bash
# Estrutura esperada
ls -R storage/app/guias/

# Deve mostrar algo como:
# storage/app/guias/2026/01/29/unimed/arquivo.pdf
# storage/app/guias/2026/01/29/bradesco-saude/arquivo.pdf
```

### 4. Verificar Logs

```bash
tail -50 storage/logs/laravel.log | grep -i "convenio"
```

## Resultado Esperado

### Antes:
```
Convênios cadastrados: 6
- Unimed
- Bradesco Saúde
- Amil
- SulAmérica
- NotreDame Intermédica
- Particular
```

### Depois do Teste:
```
Convênios cadastrados: 9
- Unimed ✓ (já existia)
- Bradesco Saúde ✓ (já existia - encontrado por similaridade)
- Amil ✓ (já existia)
- SulAmérica
- NotreDame Intermédica
- Particular
- Golden Cross ⭐ (criado automaticamente)
- Amil One ⭐ (criado automaticamente)
- Sus ⭐ (criado automaticamente)
```

## Verificação de Sucesso

✅ **Importação funcionando** se:
- Convênios novos são criados automaticamente
- Convênios existentes são encontrados (mesmo com variações)
- Pedidos são associados aos convênios corretos

✅ **Armazenamento funcionando** se:
- Documentos são salvos em: `guias/YYYY/MM/DD/convenio/`
- Pedidos sem convênio vão para: `guias/YYYY/MM/DD/sem-convenio/`
- Estrutura de pastas é criada automaticamente

## Próximos Passos

1. ✅ Execute o seeder de convênios
2. ✅ Execute o script de teste acima
3. ✅ Faça uma importação real com `php artisan import:pedidos`
4. ✅ Faça upload de documentos e verifique a estrutura de pastas
5. ✅ Verifique os logs para confirmar a criação automática

## Troubleshooting

### Erro: "SQLSTATE[HY000] [2002] Connection refused"
**Solução**: Configure o SQL Server em Configurações > Integração > Softlab

### Convênios duplicados sendo criados
**Solução**: Ajuste a lógica de normalização em `processarConvenio()`

### Pasta não criada automaticamente
**Solução**: Verifique permissões:
```bash
chmod -R 775 storage/app/guias
```
