<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('role', 'admin')->value('id');
        $now = now();

        DB::table('levels')->upsert(
            [
                [
                    'number' => 0,
                    'name' => 'Lobby',
                    'summary' => 'Um saguão silencioso entre portas que não levam ao mesmo lugar.',
                    'description' => 'O Lobby parece familiar à primeira vista, mas a disposição dos corredores muda quando ninguém está olhando. Luzes fracas marcam o caminho de volta.',
                    'danger_level' => 'safe',
                    'created_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'number' => 1,
                    'name' => 'Habitable Zone',
                    'summary' => 'Uma área ampla onde abrigo e movimento são possíveis.',
                    'description' => 'Salas conectadas formam uma zona habitável, com sinais de presença recente e recursos que aparecem em intervalos irregulares.',
                    'danger_level' => 'low',
                    'created_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'number' => 2,
                    'name' => 'Pipe Dreams',
                    'summary' => 'Túneis quentes e estreitos escondem rotas instáveis.',
                    'description' => 'Tubulações cobrem as paredes e carregam sons difíceis de localizar. O calor aumenta perto das bifurcações e desorienta exploradores.',
                    'danger_level' => 'moderate',
                    'created_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'number' => 3,
                    'name' => 'Electrical Station',
                    'summary' => 'Uma estação industrial com energia imprevisível.',
                    'description' => 'Geradores antigos alimentam corredores metálicos em ciclos irregulares. Descargas repentinas tornam cada travessia uma escolha calculada.',
                    'danger_level' => 'high',
                    'created_by' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ],
            ['number'],
            ['name', 'summary', 'description', 'danger_level', 'created_by', 'updated_at']
        );
    }
}
