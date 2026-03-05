<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ImportarUsuariosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:importar {arquivo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa usuários de um arquivo TXT (formato: NOME;CODIGO;SENHA;TIPO)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $arquivo = $this->argument('arquivo');

        if (!file_exists($arquivo)) {
            $this->error("Arquivo não encontrado: {$arquivo}");
            return 1;
        }

        $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $importados = 0;
        $erros = 0;

        $this->info("Importando usuários de: {$arquivo}");
        $bar = $this->output->createProgressBar(count($linhas));
        $bar->start();

        foreach ($linhas as $linha) {
            $dados = array_map('trim', explode(';', $linha));

            if (count($dados) < 4) {
                $this->newLine();
                $this->warn("Linha inválida (esperado 4 campos): {$linha}");
                $erros++;
                $bar->advance();
                continue;
            }

            [$nomeCompleto, $codigo, $senha, $tipo] = $dados;

            // Extrai primeiro e segundo nome
            $partesNome = explode(' ', $nomeCompleto);
            $primeiroNome = $partesNome[0] ?? '';
            $segundoNome = $partesNome[1] ?? '';
            $nomeExibicao = trim($primeiroNome . ' ' . $segundoNome);

            // Gera email único baseado no código
            $email = strtolower($codigo) . '@cacim.local';

            try {
                // Verifica se usuário já existe
                $userExistente = User::where('codigo', $codigo)->first();

                if ($userExistente) {
                    $this->newLine();
                    $this->warn("Usuário já existe: {$codigo} - {$nomeExibicao}");
                    $erros++;
                    $bar->advance();
                    continue;
                }

                User::create([
                    'nome' => $nomeExibicao,
                    'codigo' => $codigo,
                    'email' => $email,
                    'password' => Hash::make($senha),
                    'role' => 'ATENDENTE',
                    'ativo' => true,
                    'forcar_troca_senha' => false
                ]);

                $importados++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Erro ao importar {$codigo}: " . $e->getMessage());
                $erros++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Importação concluída!");
        $this->table(
            ['Resultado', 'Quantidade'],
            [
                ['Importados com sucesso', $importados],
                ['Erros/Já existentes', $erros],
                ['Total de linhas', count($linhas)]
            ]
        );

        return 0;
    }
}
