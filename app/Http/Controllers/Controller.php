<?php

namespace App\Http\Controllers;

/**
 * Controller base abstrato do Laravel.
 *
 * Todo controller da aplicação (como o HomeController) estende esta
 * classe. Ela está vazia por padrão, mas é o lugar convencional para
 * registrar traits compartilhados entre controllers (ex.: autorização,
 * validação em lote) caso o projeto cresça.
 */
abstract class Controller
{
}
