<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:backfill-codigo {--dry-run} {--domain=cacim.local}', function () {
    $domain = $this->option('domain');
    $dryRun = (bool) $this->option('dry-run');

    $users = User::whereNull('codigo')
        ->orWhere('codigo', '')
        ->orderBy('id')
        ->get();

    if ($users->isEmpty()) {
        $this->info('Nenhum usuário sem código para atualizar.');
        return;
    }

    $updated = 0;
    $skipped = 0;

    foreach ($users as $user) {
        $codigo = null;

        if (!empty($user->email) && str_contains($user->email, '@')) {
            [$local, $emailDomain] = explode('@', $user->email, 2);
            $local = trim($local);
            if ($local !== '') {
                $codigo = strtoupper($local);
            }
            if ($emailDomain !== $domain) {
                $this->warn("Usuário {$user->id} email fora do domínio {$domain}: {$user->email}");
            }
        }

        if (!$codigo) {
            $this->warn("Usuário {$user->id} sem email válido. Ignorado.");
            $skipped++;
            continue;
        }

        if (User::where('codigo', $codigo)->where('id', '!=', $user->id)->exists()) {
            $this->warn("Código {$codigo} já existe. Usuário {$user->id} ignorado.");
            $skipped++;
            continue;
        }

        if ($dryRun) {
            $this->line("[DRY] {$user->id} {$user->email} -> {$codigo}");
        } else {
            $user->codigo = $codigo;
            $user->save();
            $this->line("OK {$user->id} {$user->email} -> {$codigo}");
        }

        $updated++;
    }

    $this->info("Finalizado. Atualizados: {$updated}. Ignorados: {$skipped}.");
})->purpose('Preenche users.codigo a partir do email (local-part).');
